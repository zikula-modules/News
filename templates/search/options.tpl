{* Purpose of this template: Display search options *}
<input type="hidden" id="mUNewsActive" name="active[MUNews]" value="1" checked="checked" />
<div>
    <input type="checkbox" id="active_mUNewsMessages" name="mUNewsSearchTypes[]" value="message"{if $active_message} checked="checked"{/if} />
    <label for="active_mUNewsMessages">{gt text='Messages' domain='module_munews'}</label>
</div>
