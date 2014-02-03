{* purpose of this template: build the Form to edit an instance of message *}
{include file='admin/header.tpl'}
{pageaddvar name='javascript' value='modules/MUNews/javascript/MUNews_editFunctions.js'}
{pageaddvar name='javascript' value='modules/MUNews/javascript/MUNews_validation.js'}

{if $mode eq 'edit'}
    {gt text='Edit message' assign='templateTitle'}
    {assign var='adminPageIcon' value='edit'}
{elseif $mode eq 'create'}
    {gt text='Create message' assign='templateTitle'}
    {assign var='adminPageIcon' value='new'}
{else}
    {gt text='Edit message' assign='templateTitle'}
    {assign var='adminPageIcon' value='edit'}
{/if}
<div class="munews-message munews-edit">
    {pagesetvar name='title' value=$templateTitle}
    <div class="z-admin-content-pagetitle">
        {icon type=$adminPageIcon size='small' alt=$templateTitle}
        <h3>{$templateTitle}</h3>
    </div>
{form enctype='multipart/form-data' cssClass='z-form'}
    {* add validation summary and a <div> element for styling the form *}
    {munewsFormFrame}
    {formsetinitialfocus inputId='title'}

    <fieldset>
        <legend>{gt text='Content'}</legend>
        
        <div class="z-formrow">
            {formlabel for='title' __text='Title' mandatorysym='1' cssClass=''}
            {formtextinput group='message' id='title' mandatory=true readOnly=false __title='Enter the title of the message' textMode='singleline' maxLength=255 cssClass='required' }
            {munewsValidationError id='title' class='required'}
        </div>
        
        <div class="z-formrow">
            {formlabel for='startText' __text='Start text' mandatorysym='1' cssClass=''}
            {formtextinput group='message' id='startText' mandatory=true __title='Enter the start text of the message' textMode='multiline' rows='6' cols='50' cssClass='required' }
            {munewsValidationError id='startText' class='required'}
        </div>
        
        <div class="z-formrow">
            {formlabel for='imageUpload1' __text='Image upload1' cssClass=''}<br />{* break required for Google Chrome *}
            {formuploadinput group='message' id='imageUpload1' mandatory=false readOnly=false cssClass=' validate-upload' }
            <span class="z-formnote"><a id="resetImageUpload1Val" href="javascript:void(0);" class="z-hide" style="clear:left;">{gt text='Reset to empty value'}</a></span>
            
                <span class="z-formnote">{gt text='Allowed file extensions:'} <span id="imageUpload1FileExtensions">gif, jpeg, jpg, png</span></span>
            <span class="z-formnote">{gt text='Allowed file size:'} {'102400'|munewsGetFileSize:'':false:false}</span>
            {if $mode ne 'create'}
                {if $message.imageUpload1 ne ''}
                    <span class="z-formnote">
                        {gt text='Current file'}:
                        <a href="{$message.imageUpload1FullPathUrl}" title="{$formattedEntityTitle|replace:"\"":""}"{if $message.imageUpload1Meta.isImage} rel="imageviewer[message]"{/if}>
                        {if $message.imageUpload1Meta.isImage}
                            {thumb image=$message.imageUpload1FullPath objectid="message-`$message.id`" preset=$messageThumbPresetImageUpload1 tag=true img_alt=$formattedEntityTitle}
                        {else}
                            {gt text='Download'} ({$message.imageUpload1Meta.size|munewsGetFileSize:$message.imageUpload1FullPath:false:false})
                        {/if}
                        </a>
                    </span>
                    <span class="z-formnote">
                        {formcheckbox group='message' id='imageUpload1DeleteFile' readOnly=false __title='Delete image upload1 ?'}
                        {formlabel for='imageUpload1DeleteFile' __text='Delete existing file'}
                    </span>
                {/if}
            {/if}
            {munewsValidationError id='imageUpload1' class='validate-upload'}
        </div>
        
        <div class="z-formrow">
            {formlabel for='mainText' __text='Main text' cssClass=''}
            {formtextinput group='message' id='mainText' mandatory=false __title='Enter the main text of the message' textMode='multiline' rows='6' cols='50' cssClass='' }
        </div>
        
        <div class="z-formrow">
            {formlabel for='imageUpload2' __text='Image upload2' cssClass=''}<br />{* break required for Google Chrome *}
            {formuploadinput group='message' id='imageUpload2' mandatory=false readOnly=false cssClass=' validate-upload' }
            <span class="z-formnote"><a id="resetImageUpload2Val" href="javascript:void(0);" class="z-hide" style="clear:left;">{gt text='Reset to empty value'}</a></span>
            
                <span class="z-formnote">{gt text='Allowed file extensions:'} <span id="imageUpload2FileExtensions">gif, jpeg, jpg, png</span></span>
            <span class="z-formnote">{gt text='Allowed file size:'} {'102400'|munewsGetFileSize:'':false:false}</span>
            {if $mode ne 'create'}
                {if $message.imageUpload2 ne ''}
                    <span class="z-formnote">
                        {gt text='Current file'}:
                        <a href="{$message.imageUpload2FullPathUrl}" title="{$formattedEntityTitle|replace:"\"":""}"{if $message.imageUpload2Meta.isImage} rel="imageviewer[message]"{/if}>
                        {if $message.imageUpload2Meta.isImage}
                            {thumb image=$message.imageUpload2FullPath objectid="message-`$message.id`" preset=$messageThumbPresetImageUpload2 tag=true img_alt=$formattedEntityTitle}
                        {else}
                            {gt text='Download'} ({$message.imageUpload2Meta.size|munewsGetFileSize:$message.imageUpload2FullPath:false:false})
                        {/if}
                        </a>
                    </span>
                    <span class="z-formnote">
                        {formcheckbox group='message' id='imageUpload2DeleteFile' readOnly=false __title='Delete image upload2 ?'}
                        {formlabel for='imageUpload2DeleteFile' __text='Delete existing file'}
                    </span>
                {/if}
            {/if}
            {munewsValidationError id='imageUpload2' class='validate-upload'}
        </div>
        
        <div class="z-formrow">
            {formlabel for='imageUpload3' __text='Image upload3' cssClass=''}<br />{* break required for Google Chrome *}
            {formuploadinput group='message' id='imageUpload3' mandatory=false readOnly=false cssClass=' validate-upload' }
            <span class="z-formnote"><a id="resetImageUpload3Val" href="javascript:void(0);" class="z-hide" style="clear:left;">{gt text='Reset to empty value'}</a></span>
            
                <span class="z-formnote">{gt text='Allowed file extensions:'} <span id="imageUpload3FileExtensions">gif, jpeg, jpg, png</span></span>
            <span class="z-formnote">{gt text='Allowed file size:'} {'102400'|munewsGetFileSize:'':false:false}</span>
            {if $mode ne 'create'}
                {if $message.imageUpload3 ne ''}
                    <span class="z-formnote">
                        {gt text='Current file'}:
                        <a href="{$message.imageUpload3FullPathUrl}" title="{$formattedEntityTitle|replace:"\"":""}"{if $message.imageUpload3Meta.isImage} rel="imageviewer[message]"{/if}>
                        {if $message.imageUpload3Meta.isImage}
                            {thumb image=$message.imageUpload3FullPath objectid="message-`$message.id`" preset=$messageThumbPresetImageUpload3 tag=true img_alt=$formattedEntityTitle}
                        {else}
                            {gt text='Download'} ({$message.imageUpload3Meta.size|munewsGetFileSize:$message.imageUpload3FullPath:false:false})
                        {/if}
                        </a>
                    </span>
                    <span class="z-formnote">
                        {formcheckbox group='message' id='imageUpload3DeleteFile' readOnly=false __title='Delete image upload3 ?'}
                        {formlabel for='imageUpload3DeleteFile' __text='Delete existing file'}
                    </span>
                {/if}
            {/if}
            {munewsValidationError id='imageUpload3' class='validate-upload'}
        </div>
        
        <div class="z-formrow">
            {formlabel for='imageUpload4' __text='Image upload4' cssClass=''}<br />{* break required for Google Chrome *}
            {formuploadinput group='message' id='imageUpload4' mandatory=false readOnly=false cssClass=' validate-upload' }
            <span class="z-formnote"><a id="resetImageUpload4Val" href="javascript:void(0);" class="z-hide" style="clear:left;">{gt text='Reset to empty value'}</a></span>
            
                <span class="z-formnote">{gt text='Allowed file extensions:'} <span id="imageUpload4FileExtensions">gif, jpeg, jpg, png</span></span>
            <span class="z-formnote">{gt text='Allowed file size:'} {'102400'|munewsGetFileSize:'':false:false}</span>
            {if $mode ne 'create'}
                {if $message.imageUpload4 ne ''}
                    <span class="z-formnote">
                        {gt text='Current file'}:
                        <a href="{$message.imageUpload4FullPathUrl}" title="{$formattedEntityTitle|replace:"\"":""}"{if $message.imageUpload4Meta.isImage} rel="imageviewer[message]"{/if}>
                        {if $message.imageUpload4Meta.isImage}
                            {thumb image=$message.imageUpload4FullPath objectid="message-`$message.id`" preset=$messageThumbPresetImageUpload4 tag=true img_alt=$formattedEntityTitle}
                        {else}
                            {gt text='Download'} ({$message.imageUpload4Meta.size|munewsGetFileSize:$message.imageUpload4FullPath:false:false})
                        {/if}
                        </a>
                    </span>
                    <span class="z-formnote">
                        {formcheckbox group='message' id='imageUpload4DeleteFile' readOnly=false __title='Delete image upload4 ?'}
                        {formlabel for='imageUpload4DeleteFile' __text='Delete existing file'}
                    </span>
                {/if}
            {/if}
            {munewsValidationError id='imageUpload4' class='validate-upload'}
        </div>
        
        <div class="z-formrow">
            {formlabel for='muimageAlbum' __text='Muimage album' cssClass=''}
            {formintinput group='message' id='muimageAlbum' mandatory=false __title='Enter the muimage album of the message' maxLength=11 cssClass=' validate-digits' }
            {munewsValidationError id='muimageAlbum' class='validate-digits'}
        </div>
        
        <div class="z-formrow">
            {formlabel for='weight' __text='Weight' mandatorysym='1' cssClass=''}
            {formintinput group='message' id='weight' mandatory=true __title='Enter the weight of the message' maxLength=2 cssClass='required validate-digits' }
            {munewsValidationError id='weight' class='required'}
            {munewsValidationError id='weight' class='validate-digits'}
        </div>
        
        <div class="z-formrow">
            {formlabel for='startDate' __text='Start date' cssClass=''}
            {if $mode ne 'create'}
                {formdateinput group='message' id='startDate' mandatory=false __title='Enter the start date of the message' includeTime=true cssClass='' }
            {else}
                {formdateinput group='message' id='startDate' mandatory=false __title='Enter the start date of the message' includeTime=true cssClass='' }
            {/if}
            
            <span class="z-formnote"><a id="resetStartDateVal" href="javascript:void(0);" class="z-hide">{gt text='Reset to empty value'}</a></span>
        </div>
        
        <div class="z-formrow">
            {formlabel for='noEndDate' __text='No end date' cssClass=''}
            {formcheckbox group='message' id='noEndDate' readOnly=false __title='no end date ?' cssClass='' }
        </div>
        
        <div class="z-formrow">
            {formlabel for='endDate' __text='End date' cssClass=''}
            {if $mode ne 'create'}
                {formdateinput group='message' id='endDate' mandatory=false __title='Enter the end date of the message' includeTime=true cssClass='' }
            {else}
                {formdateinput group='message' id='endDate' mandatory=false __title='Enter the end date of the message' includeTime=true cssClass='' }
            {/if}
            
            <span class="z-formnote"><a id="resetEndDateVal" href="javascript:void(0);" class="z-hide">{gt text='Reset to empty value'}</a></span>
        </div>
    </fieldset>
    
    {include file='admin/include_categories_edit.tpl' obj=$message groupName='messageObj'}
    {if $mode ne 'create'}
        {include file='admin/include_standardfields_edit.tpl' obj=$message}
    {/if}
    
    {* include display hooks *}
    {if $mode ne 'create'}
        {assign var='hookId' value=$message.id}
        {notifydisplayhooks eventname='munews.ui_hooks.messages.form_edit' id=$hookId assign='hooks'}
    {else}
        {notifydisplayhooks eventname='munews.ui_hooks.messages.form_edit' id=null assign='hooks'}
    {/if}
    {if is_array($hooks) && count($hooks)}
        {foreach key='providerArea' item='hook' from=$hooks}
            <fieldset>
                {$hook}
            </fieldset>
        {/foreach}
    {/if}
    
    {* include return control *}
    {if $mode eq 'create'}
        <fieldset>
            <legend>{gt text='Return control'}</legend>
            <div class="z-formrow">
                {formlabel for='repeatCreation' __text='Create another item after save'}
                    {formcheckbox group='message' id='repeatCreation' readOnly=false}
            </div>
        </fieldset>
    {/if}
    
    {* include possible submit actions *}
    <div class="z-buttons z-formbuttons">
    {foreach item='action' from=$actions}
        {assign var='actionIdCapital' value=$action.id|@ucwords}
        {gt text=$action.title assign='actionTitle'}
        {*gt text=$action.description assign='actionDescription'*}{* TODO: formbutton could support title attributes *}
        {if $action.id eq 'delete'}
            {gt text='Really delete this message?' assign='deleteConfirmMsg'}
            {formbutton id="btn`$actionIdCapital`" commandName=$action.id text=$actionTitle class=$action.buttonClass confirmMessage=$deleteConfirmMsg}
        {else}
            {formbutton id="btn`$actionIdCapital`" commandName=$action.id text=$actionTitle class=$action.buttonClass}
        {/if}
    {/foreach}
        {formbutton id='btnCancel' commandName='cancel' __text='Cancel' class='z-bt-cancel'}
    </div>
    {/munewsFormFrame}
{/form}
</div>
{include file='admin/footer.tpl'}

{icon type='edit' size='extrasmall' assign='editImageArray'}
{icon type='delete' size='extrasmall' assign='removeImageArray'}


<script type="text/javascript">
/* <![CDATA[ */

    var formButtons, formValidator;

    function handleFormButton (event) {
        var result = formValidator.validate();
        if (!result) {
            // validation error, abort form submit
            Event.stop(event);
        } else {
            // hide form buttons to prevent double submits by accident
            formButtons.each(function (btn) {
                btn.addClassName('z-hide');
            });
        }

        return result;
    }

    document.observe('dom:loaded', function() {

        munewsAddCommonValidationRules('message', '{{if $mode ne 'create'}}{{$message.id}}{{/if}}');
        {{* observe validation on button events instead of form submit to exclude the cancel command *}}
        formValidator = new Validation('{{$__formid}}', {onSubmit: false, immediate: true, focusOnError: false});
        {{if $mode ne 'create'}}
            var result = formValidator.validate();
        {{/if}}

        formButtons = $('{{$__formid}}').select('div.z-formbuttons input');

        formButtons.each(function (elem) {
            if (elem.id != 'btnCancel') {
                elem.observe('click', handleFormButton);
            }
        });

        Zikula.UI.Tooltips($$('.munews-form-tooltips'));
        munewsInitUploadField('imageUpload1');
        munewsInitUploadField('imageUpload2');
        munewsInitUploadField('imageUpload3');
        munewsInitUploadField('imageUpload4');
        munewsInitDateField('startDate');
        munewsInitDateField('endDate');
    });

/* ]]> */
</script>
