{* purpose of this template: module configuration *}
{include file='admin/header.tpl'}
{if $legacy eq false}
<p class="z-warningmsg">{gt text='Legacy mode is disabled. If you want to use MUImage albums, you have to enable!'}</p>
{/if}
{if $muimage eq false}
<p class="z-warningmsg">{gt text='The module MUImage is not available. If you want to use MUImage albums, you have to install it and create albums!'}</p>
{/if}
<div class="munews-config">
    {gt text='Settings' assign='templateTitle'}
    {pagesetvar name='title' value=$templateTitle}
    <div class="z-admin-content-pagetitle">
        {icon type='config' size='small' __alt='Settings'}
        <h3>{$templateTitle}</h3>
    </div>

    {form cssClass='z-form'}
        {* add validation summary and a <div> element for styling the form *}
        {munewsFormFrame}
            {formsetinitialfocus inputId='showAuthor'}
            {gt text='Settings' assign='tabTitle'}
            <fieldset>
                <legend>{$tabTitle}</legend>
            
                <p class="z-confirmationmsg">{gt text='Here you can manage all basic settings for this application.'}</p>
            
                <div class="z-formrow">
                    {formlabel for='showAuthor' __text='Show author' cssClass=''}
                        {formcheckbox id='showAuthor' group='config'}
                </div>
                <div class="z-formrow">
                    {formlabel for='showDate' __text='Show date' cssClass=''}
                        {formcheckbox id='showDate' group='config'}
                </div>
                <div class="z-formrow">
                    {formlabel for='newsPerPage' __text='News per page' cssClass=''}
                        {formintinput id='newsPerPage' group='config' maxLength=255 __title='Enter the news per page. Only digits are allowed.'}
                </div>
                <div class="z-formrow">
                    {formlabel for='allowedFileSize' __text='Allowed file size' cssClass=''}
                        {formintinput id='allowedFileSize' group='config' maxLength=255 __title='Enter the news per page. Only digits are allowed.'}
                </div>
                {if $useMUImage eq true}
                <div class="z-formrow">
                    {formlabel for='muimageAlbum' __text='Muimage album' cssClass=''}
                        {formcheckbox id='muimageAlbum' group='config'}
                </div>
                {/if}
            </fieldset>

            <div class="z-buttons z-formbuttons">
                {formbutton commandName='save' __text='Update configuration' class='z-bt-save'}
                {formbutton commandName='cancel' __text='Cancel' class='z-bt-cancel'}
            </div>
        {/munewsFormFrame}
    {/form}
</div>
{include file='admin/footer.tpl'}
