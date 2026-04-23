<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>{$appname} - {$title}</title>

{if $googletagmanagercccode ne ''}
	{include file="includes/googletagmanager.tpl" googletagmanagercccode=$googletagmanagercccode}
{/if}

{include file="includes/maininclude.tpl"}
</head>

<body>
{include file="header_large.tpl"}
<div width="100%" align="center">
<table class="coverList" cellpadding="0" cellspacing="0" style="border:0px">
	{assign var="item" value="0"}
	{assign var="col" value="0"}
	{assign var="lastpageref" value=""}
	{assign var="pagejoin" value="`$pagejoinstart`"}
	<tr>
		{foreach from=$thumblist item=row}
			{assign var="item" value="`$item+1`"}
			
			{assign var="isspread" value="0"}
			{assign var="pagejoin" value="`$pagejoin-1`"}
			{if $pagejoin==0}
				{assign var="separate" value="1"}
				{assign var="pagejoin" value="`$pagejoincount`"}
			{else}
				{assign var="separate" value="0"}
				{assign var="isspread" value="`$isspreads`"}
			{/if}
			{if ($item==1)&&($hasfrontcover==1)}
				{assign var="separate" value="1"}
			{/if}
			{if ($lastpageref=='c')||($lastpageref=='fc')||($col==$colcount)||($item==$insiderightindex)||(($col==($colcount-1))&&($pagejoin==1))||(($col>0)&&($row.pageref=="bc"))}
				</tr></table>
				<table cellpadding="0" cellspacing="0" style="border:0px"><tr class="text2"><td>&nbsp;</td></tr>
				<tr class="text">
				{assign var="col" value="1"}
			{else}
				{assign var="col" value="`$col+1`"}
			{/if}
			
			{assign var="splitimage" value="0"}
			{if ($isspreads==1)&&($outputspreads==1)}
				{if (! (($row.pageref=='fc')||($row.pageref=='c')||($row.pageref=='bc')||($item==$insideleftindex)||($item==$insiderightindex)))}
					{assign var="splitimage" value="1"}
					{assign var="halfthumbwidth" value="`$row.thumbwidth/2`"}
					{assign var="rightthumbpos" value="`$row.thumbheight/2`"}
				{/if}
			{/if}
			
			<td valign="top">
				<table cellpadding="0" cellspacing="0" align="center" >
					<tr>
						{if $item==$insideleftindex}
							{assign var="col" value="`$col+1`"}{assign var="separate" value="1"}{assign var="isspread" value="0"}
							<td>
								<div style="position:relative; height:{$row.thumbheight+2}px; width:{$row.thumbwidth*2+2}; border:1px solid">
									<div style="position:absolute; left:{$row.thumbwidth}px"><img src="{$imageurl}{$row.pageref}.jpg" width="{$row.thumbwidth}" height="{$row.thumbheight}"></div>
									<div style="position:absolute; left:{$row.thumbwidth}px; height:{$row.thumbheight}px; width:0px; border:1px solid"></div>
								</div>
							</td>
						{elseif $item==$insiderightindex}
							{assign var="col" value="`$col+1`"}{assign var="separate" value="1"}{assign var="isspread" value="0"}
							<td>
								<div style="position:relative; height:{$row.thumbheight+2}px; width:{$row.thumbwidth*2+2}; border:1px solid">
									<div style="position:absolute; left:0px"><img src="{$imageurl}{$row.pageref}.jpg" width="{$row.thumbwidth}" height="{$row.thumbheight}"></div>
									<div style="position:absolute; left:{$row.thumbwidth}px; height:{$row.thumbheight}px; width:0px; border:1px solid"></div>
								</div>
							</td>
						{else}
							<td>
								{if $splitimage==0}
									<img style="border:1px solid" src="{$imageurl}{$row.pageref}.jpg" width="{$row.thumbwidth}" height="{$row.thumbheight}">
								{else}
									<div style="position:relative; height:{$row.thumbheight+2}px; width:{$row.thumbwidth+2}; border:1px solid">
										<div style="position:absolute; clip:rect(auto {$halfthumbwidth}px auto 0px)"><img src="{$imageurl}{$row.pageref}.jpg" width="{$row.thumbwidth}" height="{$row.thumbheight}"></div>
										<div style="position:absolute; clip:rect(auto {$row.thumbwidth}px auto {$halfthumbwidth}px)"><img src="{$imageurl}{$row.pageref}.jpg" width="{$row.thumbwidth}" height="{$row.thumbheight}"></div>
										<div style="position:absolute; left:{$halfthumbwidth}px; height:{$row.thumbheight}px; width:0px; border:1px solid"></div>
									</div>
								{/if}
							</td>
						{/if}
						
						{if $separate==1}<td style="width:10px">&nbsp;</td>{/if}
					</tr>
					<tr class="text3" align="center">
						{if $isspread==1}
							<td colspan="2">{$row.pagename}</td>
						{else}
							<td>{$row.pagename}</td>
						{/if}
					</tr>
				</table>
			</td>
			{assign var="lastpageref" value="`$row.pageref`"}
		{/foreach}
	</tr>
</table>
</div>

</body>
</html>