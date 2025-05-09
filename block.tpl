{**
 * plugins/blocks/supportBlock/block.tpl
 *
 * Copyright (c) 2023-2025 Paideia Studio
 * Distributed under the GNU GPL v3.
 *
 * Bloque de soporte técnico
 *}

<div class="pkp_block block_support">
	<h2 class="title">{translate key="plugins.blocks.supportBlock.title"}</h2>
	<div class="content">
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
