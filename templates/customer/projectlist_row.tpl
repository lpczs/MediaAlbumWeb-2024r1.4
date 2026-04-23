<div class="panel projects-list {$panelclass}" id="{$project.projectref}" data-name="{$project.name}">
    <div class="panel-inner">
        <div class="project-preview-wrap">
            {assign var='thumbnailpath' value=''}
            {if $project.thumbnailpath != ''}
               {assign var='thumbnailpath' value="{$onlinedesignerurl}{$project.thumbnailpath|escape}"}
            {/if}

            {if $project.projectpreviewthumbnail != ''}
                <img src="{$project.projectpreviewthumbnail|escape}" class="product-preview-image" data-asset="{$thumbnailpath}" alt="" />
            {else if $thumbnailpath != ''}
                <img src="{$thumbnailpath}" class="product-preview-image" data-asset="" alt="" />
            {else}
                <img src="{$brandroot}/images/no_image-2x.jpg" class="product-preview-image" alt="" />
            {/if}
        </div>

        <div class="project-details">
            
            <h2 id="projectname">{$project.name}</h2>
            <p>{$project.productname}</p>
            {if $project.dateofpurge != ''}
            <p class="date-of-purge">{#str_MessageProjectDueToBePurged#} {$project.dateofpurge} <a href="#" class="keepProjectLink" data-projectref="{$project.projectref}">{#str_MessageKeepProject#}</a></p>
            {/if}
            <p>{#str_LabelCreated#} {$project.datecreated}</p>

            {if ($project.canedit)}
            <button class="button action continue">{#str_ButtonContinueEditing#}</button>
            {else}
            <div class="no-button">
                <p>{#str_MessageProjectCannotBeEdited#}</p>
            </div>
            {/if}
            
            <ul class="project-options"> 
                <li>
                    <button class="duplicate">{#str_ButtonDuplicateProject#}</button>
                </li>
                <li>
                    <button class="delete" {($project.candelete)?"":"disabled"}>{#str_ButtonDeleteProject#}</button>
                </li>
                <li class="rename">
                    <button class="rename">{#str_ButtonRenameProject#}</button>
                </li>
				<li class="share">
					<button class="share">{#str_LabelShare#}</button>
				</li>
            </ul>
            
        </div>
    </div>
</div>