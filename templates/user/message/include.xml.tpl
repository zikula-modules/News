{* purpose of this template: messages xml inclusion template in user area *}
<message id="{$item.id}" createdon="{$item.createdDate|dateformat}" updatedon="{$item.updatedDate|dateformat}">
    <id>{$item.id}</id>
    <title><![CDATA[{$item.title}]]></title>
    <startText><![CDATA[{$item.startText}]]></startText>
    <imageUpload1{if $item.imageUpload1 ne ''} extension="{$item.imageUpload1Meta.extension}" size="{$item.imageUpload1Meta.size}" isImage="{if $item.imageUpload1Meta.isImage}true{else}false{/if}"{if $item.imageUpload1Meta.isImage} width="{$item.imageUpload1Meta.width}" height="{$item.imageUpload1Meta.height}" format="{$item.imageUpload1Meta.format}"{/if}{/if}>{$item.imageUpload1}</imageUpload1>
    <mainText><![CDATA[{$item.mainText}]]></mainText>
    <imageUpload2{if $item.imageUpload2 ne ''} extension="{$item.imageUpload2Meta.extension}" size="{$item.imageUpload2Meta.size}" isImage="{if $item.imageUpload2Meta.isImage}true{else}false{/if}"{if $item.imageUpload2Meta.isImage} width="{$item.imageUpload2Meta.width}" height="{$item.imageUpload2Meta.height}" format="{$item.imageUpload2Meta.format}"{/if}{/if}>{$item.imageUpload2}</imageUpload2>
    <imageUpload3{if $item.imageUpload3 ne ''} extension="{$item.imageUpload3Meta.extension}" size="{$item.imageUpload3Meta.size}" isImage="{if $item.imageUpload3Meta.isImage}true{else}false{/if}"{if $item.imageUpload3Meta.isImage} width="{$item.imageUpload3Meta.width}" height="{$item.imageUpload3Meta.height}" format="{$item.imageUpload3Meta.format}"{/if}{/if}>{$item.imageUpload3}</imageUpload3>
    <imageUpload4{if $item.imageUpload4 ne ''} extension="{$item.imageUpload4Meta.extension}" size="{$item.imageUpload4Meta.size}" isImage="{if $item.imageUpload4Meta.isImage}true{else}false{/if}"{if $item.imageUpload4Meta.isImage} width="{$item.imageUpload4Meta.width}" height="{$item.imageUpload4Meta.height}" format="{$item.imageUpload4Meta.format}"{/if}{/if}>{$item.imageUpload4}</imageUpload4>
    <muimageAlbum>{$item.muimageAlbum}</muimageAlbum>
    <weight>{$item.weight}</weight>
    <startDate>{$item.startDate|dateformat:'datetimebrief'}</startDate>
    <noEndDate>{if !$item.noEndDate}0{else}1{/if}</noEndDate>
    <endDate>{$item.endDate|dateformat:'datetimebrief'}</endDate>
    <options>{$item.options}</options>
    <relationTo>{$item.relationTo}</relationTo>
    <workflowState>{$item.workflowState|munewsObjectState:false|lower}</workflowState>
</message>
