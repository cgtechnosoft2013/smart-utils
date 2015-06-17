

/**
 * Events :
 * cascade_select.after_populate
 *
 *
 * Definitions:
 *
 * OPTION: JS Object describing an html option and children (next level options)
 * Example:
 *  var option = { 'label': 'AB', 'value': 12, children: [
 *                   { 'label': 'ABA', 'value': 121 }
 *               ]};
 *
 * PATH: defines all selected values form level 1 to X
 * Examples:
 *  var path = [[5],[3]]; (single select case)
 *  var path = [[5, 6],[3, 12, 13], [1]]; (multiple select case)
 *
 *
 */
+function($) {

    // CASCADESELECT PUBLIC CLASS DEFINITION
    // =====================================

    var CascadeSelect = function(element, options) {
        this.init(element, options);
    };

    CascadeSelect.VERSION = '1.0.0';

    CascadeSelect.DEFAULTS = {
        "data": {},
        "selects": []
    };

    CascadeSelect.DEFAULTS.fnAddChangeListeners = function() {

        for(var level=1; level <= this.options.selects.length; level++) {
            $.proxy(this.options.fnAddChangeListener, this)(level);
        }
    };

    CascadeSelect.DEFAULTS.fnAddChangeListener = function(level) {

        var self = this;

        this.getLevelSelect(level).change(function(){
            $.proxy(self.options.fnPopulateNextLevels, self)(level);
        });

    };

    CascadeSelect.DEFAULTS.fnPopulateNextLevels = function(level) {

        if(level >= this.maxLevel) {
            return false;
        }

        // manage empty selection
        if (this.getLevelValues(level).length === 0 || (this.getLevelValues(level).length === 1 && $.inArray("", this.getLevelValues(level)) === 0)) {
            $.proxy(this.options.fnRemoveNextSelections, this)(level+1);
        }else{

            var path = this.createPathFromLevel(level);
            var options = this.getLastSelectedLevelOptionsFromPath(path);

            $.proxy(this.options.fnPouplateLevelWithData, this)(level+1, options);
        }

        // auto call with next level
        $.proxy(this.options.fnPopulateNextLevels, this)(level+1, options);


    };


    CascadeSelect.DEFAULTS.fnPouplateLevelWithData = function(level, options) {

        var $select = this.getLevelSelect(level);

        // save selected to reapply after option update
        var selectionValue = [];
        $select.find('option:selected').each(function(){
            selectionValue.push($(this).attr('value'));
        });

        $select.html('');
        if($select.hasClass('select2')) {
            $select.select2('val', null);
        }else if($select.hasClass('chosen-select')){
            $select.trigger("chosen:updated");
        } 

        if(!$select.attr('multiple')) {
            $select.append('<option value="">' + this.escape(this.options.selects[level-1].emptyLabel) + '</option>');
        }

        // sort in groups
        if(options.length > 0) {
            options.sort(function(a, b) {
                return a.group > b.group;
            });
        }

        var currentGroup = null;
        var $currentGroup = null;
        for(var index in options) {

            if(typeof options[index].group !== 'undefined' && currentGroup !== this.escape(options[index].group)) {
                currentGroup = this.escape(options[index].group);
                // try to find existent optiongoup, or create new one
                $currentGroup = $select.find('optgroup[label="' + currentGroup + '"]');
                if($currentGroup.length === 0) {
                    $select.append('<optgroup/>');
                    $select.find('optgroup').last().attr('label', currentGroup) // XSS protection, label must be defined outside $.append
                    $currentGroup = $select.find('optgroup[label="' + currentGroup + '"]');
                }
            }

            var dataHtml = ''; // be carefull, options have to be XSS ecaped where there're used
            if(typeof options[index].data === 'object') {
                for(var dataIndex in options[index].data) {
                    dataHtml += 'data-' + dataIndex + '="' + options[index].data[dataIndex] + '" ';
                }
            }

            if(null !== currentGroup) {
                var $attachment = $currentGroup;
            } else {
                $attachment = $select;
            }
            $attachment.append('<option ' + dataHtml + ' />');
            $attachment.find('option').last()
                .val(this.escape(options[index].value))
                .text(options[index].label);
        }

        // re-select previously selected option (multiple select case only)
        for(var i=0; i<selectionValue.length; i++) {
            $select.find('option[value="' + selectionValue[i] + '"]').prop('selected', true).change();
        }

        this.$container.trigger('cascade_select.after_populate', [level, options]);
    };

    CascadeSelect.DEFAULTS.fnPopulateFirstLevel = function() {


        for(var level=1; level < this.options.selects.length; level++) {
            var data = this.options.fnGetOptionsForLevel(level, this.options.data);
            var directStart = this.options.selects[level - 1].directStart;
            if(typeof directStart !== 'undefined' && directStart || level === 1){
                $.proxy(this.options.fnPouplateLevelWithData, this)(level, data);
            }
        }

    };

    CascadeSelect.DEFAULTS.fnGetOptionsForLevel = function(level, data) {
        if (level === 1){
            return data;
        }
        var children = [];
        $.each(data, function(index, select){
            $.each(select.children, function(index, child){
                children.push(child);
            });
        });
        return this.fnGetOptionsForLevel(level-1, children);
    };


    CascadeSelect.DEFAULTS.fnRemoveNextSelections = function(level) {

        for(var l=level; l<=this.maxLevel; l++) {
            var directStart = this.options.selects[l - 1].directStart;
            if(typeof directStart !== 'undefined' && directStart){
                var data = this.options.fnGetOptionsForLevel(l, this.options.data);
                $.proxy(this.options.fnPouplateLevelWithData, this)(l, data);
            }else{
                $.proxy(this.options.fnPouplateLevelWithData, this)(l, {});
            }
        }
    };

    /**
     * If last level is defined (previous data for example, recrate the whole selection
     */
    CascadeSelect.DEFAULTS.fnApplyLastLevelSelection = function() {

        var selectedValues = this.getLevelValues(this.maxLevel);

        var path = this.createPathFromLastLevelValues(selectedValues);
        this.applyPathSelection(path);
    };




    // PROTOTYPE CUSTOMISABLE FUNCTIONS


    /**
     * initialize with option array and bind application events
     */
    CascadeSelect.prototype.init = function(container, options) {

        this.$container = $(container);
        this.options = this.getOptions(options);
        this.maxLevel = this.options.selects.length;

        $.proxy(this.options.fnPopulateFirstLevel, this)();
        $.proxy(this.options.fnAddChangeListeners, this)();
        $.proxy(this.options.fnApplyLastLevelSelection, this)();

        //var test = this.getLastSelectedLevelOptionsFromPath([[1,2],[12, 22]]);

    };


    CascadeSelect.prototype.getDefaults = function() {
        return CascadeSelect.DEFAULTS;
    };

    CascadeSelect.prototype.getOptions = function(options) {
        options = $.extend({}, this.getDefaults(), options);
        return options;
    };

    CascadeSelect.prototype.getLevelSelect = function(level) {
        return $(this.options.selects[level-1].selector);
    };

    CascadeSelect.prototype.getLevelValues = function(level) {

        var $select = this.getLevelSelect(level);
        var selectedValues = [];

        for(var i=0; i < $select.find('option:selected').length; i++ ) {

            var value = $($select.find('option:selected')[i]).attr('value'); // != val() if no value attribute

            if(typeof value !== 'undefined') {
                selectedValues.push(value);
            }
        }

        return selectedValues;
    };

    CascadeSelect.prototype.createPathFromLevel = function(level) {

        var path = [];

        for(var currentLevel=1; currentLevel<=level; currentLevel++) {
            var values = this.getLevelValues(currentLevel);
            path.push(values);
        }

        return path;
    };


    /**
     * Return array of options for Level = path.length + 1
     *
     * @param array path : Array of selected values in selects controls, format : [[1,2], [1]] (to allow multiple select)
     */
    CascadeSelect.prototype.getLastSelectedLevelOptionsFromPath = function(path) {

        // initialize on first level with data (=level 1 options)
        var options = this.options.fnGetOptionsForLevel(path.length, this.options.data);

        return this.getNextLevelOptions(options, path[path.length - 1]);
    };

    CascadeSelect.prototype.getNextLevelOptions = function(levelOptions, selectedValues) {

        var nextLevelOptions = [];

        for(var i=0; i<selectedValues.length; i++) {

            for(var j=0; j<levelOptions.length; j++) {

                if(levelOptions[j].value == selectedValues[i]) {

                    // add all children options
                    for(var k=0; k<levelOptions[j].children.length; k++) {
                        nextLevelOptions.push(levelOptions[j].children[k]);
                    }
                }
            }
        }

        return nextLevelOptions;
    };

    /**
     * ATTENTION : NON COMPATIBLE AVEC UN FUTUR FONCTIONNEMENT AJAX
     *
     * Create path with list of selected values for each level
     * Format : [ [level1Values], [level2Values] ...]
     *
     */
    CascadeSelect.prototype.createPathFromLastLevelValues = function(searchValues) {

        var path = this.createPathFromLevelValues(searchValues, 1, this.options.data);
        return path;
    };

    /**
     * Recursive function, starting to level 1 to maxLevel
     *
     * Add Path values at each level
     */
    CascadeSelect.prototype.createPathFromLevelValues = function(searchValues, level, levelOptions) {

        var levelPath = [];
        var mergedPath = [[]];

        if(level < this.maxLevel) {

            for(var i=0; i<levelOptions.length; i++) {
                var path = this.createPathFromLevelValues(searchValues, level+1, levelOptions[i].children);

                if(path[0].length > 0) {
                    mergedPath = this.mergePath(mergedPath, path);
                    levelPath.push(levelOptions[i].value);
                }
            }

            mergedPath.unshift(levelPath);
            return mergedPath;
        }


        for(var i=0; i<searchValues.length; i++) {

            for(var j=0; j<levelOptions.length; j++) {

                if(levelOptions[j].value == searchValues[i]) {
                    levelPath.push(searchValues[i]);
                }
            }
        }

        return [levelPath];
    };

    CascadeSelect.prototype.applyPathSelection = function(path) {

        for(var l=0; l<path.length; l++) {
            var $select = this.getLevelSelect(l+1);

            for(var i=0; i<path[l].length; i++) {
                $select.find('option[value="' + path[l][i] + '"]').prop('selected', true).change();
            }

            // define next level list
            $.proxy(this.options.fnPopulateNextLevels, this)(l+1);
        }
    };

    CascadeSelect.prototype.mergePath = function(path1, path2) {

        // path1.length is <=


        var mergedPath = [[]];

        for(var i=0; i<path2.length; i++) {

            if(typeof path1[i] !== 'undefined') {
                mergedPath[i] = path1[i].concat(path2[i]);
            } else {
                mergedPath[i] = path2[i];
            }
        }

        return mergedPath;
    };

    CascadeSelect.prototype.escape = function(codeToEscape) {
        var entityMap = {
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': '&quot;'
        };

        return String(codeToEscape).replace(/[&<>"]/g, function (s) {
            return entityMap[s];
        });
    };

    CascadeSelect.prototype.updateData = function(data) {
        this.options.data = data;
        $.proxy(this.options.fnPopulateFirstLevel, this)();
    }

    // CASCADESELECT PLUGIN DEFINITION
    // ===============================

    function Plugin(option) {

        if (typeof option == 'string') {
            switch(option) {
                case 'getInstance':
                    var data = $(this).data('bs.cascadeselect');
                    return data;
            }
        }

        return this.each(function() {
            var $this = $(this);
            var data = $this.data('bs.cascadeselect');
            var options = typeof option == 'object' && option;

            if (!data) {
                // save object with option
                $this.data('bs.cascadeselect', (data = new CascadeSelect(this, options)));
            }

        });
    }

    $.fn.cascadeselect = Plugin;
    $.fn.cascadeselect.Constructor = CascadeSelect;

}(jQuery);
