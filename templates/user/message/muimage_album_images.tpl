            {foreach item=item from=$images}
                {if $item.imageUpload ne '' && isset($item.imageUploadFullPathURL)}
                    <a href="{$item.imageUploadFullPathURL}" title="{$item.title|replace:"\"":""}"{if $item.imageUploadMeta.isImage} rel="imageviewer[item]"{/if}>
                        {if $item.imageUploadMeta.format eq 'landscape'}
                            <img src="{$item.imageUpload|muimageImageThumb:$item.imageUploadFullPath:100:70}" width="100" height="70" alt="{$item.title|replace:"\"":""}" />
                        {/if}
                        {if $item.imageUploadMeta.format eq 'portrait'}
                            <img src="{$item.imageUpload|muimageImageThumb:$item.imageUploadFullPath:52:70}" width="52" height="70" alt="{$item.title|replace:"\"":""}" />
                        {/if} 

                    </a>
                {/if}
            {/foreach}