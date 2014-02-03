{* purpose of this template: messages view json view in user area *}
{munewsTemplateHeaders contentType='application/json'}
[
{foreach item='item' from=$items name='messages'}
    {if not $smarty.foreach.messages.first},{/if}
    {$item->toJson()}
{/foreach}
]
