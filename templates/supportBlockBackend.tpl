{**
 * plugins/blocks/supportBlock/templates/supportBlockBackend.tpl
 *
 * Copyright (c) 2023-2025 Paideia Studio
 * Distributed under the GNU GPL v3.
 *
 * Bloque de soporte técnico para el backend
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
</div>