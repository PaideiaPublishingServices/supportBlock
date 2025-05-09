{**
 * plugins/blocks/supportBlock/templates/supportBlockAdmin.tpl
 *
 * Copyright (c) 2023-2025 Paideia Studio
 * Distributed under the GNU GPL v3.
 *
 * Bloque de soporte técnico para el área de administración
 *}

<div class="pkp_block block_support">
    <div class="content">
        <div class="block_support_title">{translate key="plugins.blocks.supportBlock.title"}</div>
        <ul>
            <li class="support_ticket">
                <span class="support_icon">
                    <i class="fa fa-life-ring" aria-hidden="true"></i>
                </span>
                <a href="{$supportUrl}" target="_blank">{translate key="plugins.blocks.supportBlock.openTicket"}</a>
            </li>
        </ul>
        <div class="hosted_by">
            <span>{translate key="plugins.blocks.supportBlock.hostedBy"}</span>
            <strong>Paideia Studio</strong>
        </div>
    </div>
    <style>
    .pkp_block.block_support {
        margin-bottom: 15px;
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 3px;
        background-color: #f8f8f8;
    }
    
    .pkp_block.block_support .block_support_title {
        margin-top: 0;
        margin-bottom: 10px;
        font-size: 1.2em;
        font-weight: 700;
        color: #333;
    }
    
    .pkp_block.block_support ul {
        padding: 0;
        margin: 0 0 15px 0;
        list-style-type: none;
    }
    
    .pkp_block.block_support li {
        margin-bottom: 8px;
        display: flex;
        align-items: center;
    }
    
    .pkp_block.block_support .support_icon {
        margin-right: 7px;
        color: #007ab2;
    }
    
    .pkp_block.block_support a {
        color: #007ab2;
        font-weight: 600;
        text-decoration: none;
    }
    
    .pkp_block.block_support a:hover {
        text-decoration: underline;
    }
    
    .pkp_block.block_support .hosted_by {
        padding-top: 10px;
        border-top: 1px solid #ddd;
        font-size: 0.9em;
        color: #666;
    }
    
    .pkp_block.block_support .hosted_by strong {
        font-weight: 600;
        color: #333;
    }
    </style>
</div>