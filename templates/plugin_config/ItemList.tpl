{* Purpose of this template: Display an edit form for configuring the newsletter plugin *}
{assign var='objectTypes' value=$plugin_parameters.MUNews_NewsletterPlugin_ItemList.param.ObjectTypes}
{assign var='pageArgs' value=$plugin_parameters.MUNews_NewsletterPlugin_ItemList.param.Args}

{assign var='j' value=1}
{foreach key='objectType' item='objectTypeData' from=$objectTypes}
    <hr />
    <div class="z-formrow">
        <label for="plugin_{$i}_enable_{$j}">{$objectTypeData.name|safetext}</label>
        <input id="plugin_{$i}_enable_{$j}" type="checkbox" name="MUNewsObjectTypes[{$objectType}]" value="1"{if $objectTypeData.nwactive} checked="checked"{/if} />
    </div>
    <div id="plugin_{$i}_suboption_{$j}">
        <div class="z-formrow">
            <label for="mUNewsArgs_{$objectType}_sorting">{gt text='Sorting'}:</label>
                <select id="mUNewsArgs_{$objectType}_sorting" name="MUNewsArgs[{$objectType}][sorting]">
                    <option value="random"{if $pageArgs.$objectType.sorting eq 'random'} selected="selected"{/if}>{gt text='Random'}</option>
                    <option value="newest"{if $pageArgs.$objectType.sorting eq 'newest'} selected="selected"{/if}>{gt text='Newest'}</option>
                    <option value="alpha"{if $pageArgs.$objectType.sorting eq 'default' || ($pageArgs.$objectType.sorting != 'random' && $pageArgs.$objectType.sorting != 'newest')} selected="selected"{/if}>{gt text='Default'}</option>
                </select>
        </div>
        <div class="z-formrow">
            <label for="mUNewsArgs_{$objectType}_amount">{gt text='Amount'}:</label>
                <input type="text" id="mUNewsArgs_{$objectType}_amount" name="MUNewsArgs[{$objectType}][amount]" value="{$pageArgs.$objectType.amount|default:'5'}" maxlength="2" size="10" />
        </div>
        
        <div class="z-formrow z-hide">
            <label for="mUNewsArgs_{$objectType}_filter">{gt text='Filter (expert option)'}:</label>
                <input type="text" id="mUNewsArgs_{$objectType}_filter" name="MUNewsArgs[{$objectType}][filter]" value="{$pageArgs.$objectType.filter|default:''}" size="40" />
                <span class="z-sub z-formnote">
                    ({gt text='Syntax examples'}: <kbd>name:like:foobar</kbd> {gt text='or'} <kbd>status:ne:3</kbd>)
                </span>
        </div>
    </div>
    {assign var='j' value=$j+1}
{foreachelse}
    <div class="z-warningmsg">{gt text='No object types found.'}</div>
{/foreach}

