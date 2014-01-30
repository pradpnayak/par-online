<div class='donationSummaryDiv'>
{foreach from=$summaryDetails item=details key=opId}
{assign var="thisMonth" value=$details.month}
{assign var="thisYear" value=$details.year}
{assign var="thisMonthAnticipated" value=$details.monthAnticipated}

<div id=donation>
    {if $congregationName.$opId neq 'dontcare'}
      <p class=subtitle><b>{$congregationName.$opId}</b></p>
    {/if}
    <p class=subtitle><b>Current Month's Donations</b></p>
    <table style = "width:250px">
    {foreach from=$thisMonth item=field key=rowName}

    { if $rowName NEQ 'total' AND $rowName NEQ 'avg' }
    <tr>
      <td>
       {$rowName}
      </td> 
      <td>
       {$field|crmMoney}
      </td>
    </tr>
    {/if}
    {if $rowName EQ 'avg'}
        {assign var = 'avg' value = $field }
    {/if}	
    {if $rowName EQ 'total'}
	{assign var = 'total' value = $field }
    {/if}
    {/foreach}
    <tr>
    <td></td><td></td>
    </tr>
    <tr>
      <td>
      	Avg
      </td> 
      <td>
       {$avg|crmMoney}
      </td>
    </tr>
    <tr>
      <td>
      	Total
      </td> 
      <td>
       {$total|crmMoney}
      </td>
    </tr>
    <tr>
      <td><div><a href={$mtd}>More Details</a></div></td>
      <td></td>
    </tr>
    </table>
    
    <p class=subtitle><b>Upcoming Month's Anticipated Donations</b></p>
    <table style = "width:250px">
    {foreach from=$thisMonthAnticipated item=field key=rowName}
    { if $rowName NEQ 'total' AND $rowName NEQ 'avg' }
    <tr>
      <td>
       {$rowName}
      </td> 
      <td>
       {$field|crmMoney}
      </td>
    </tr>
    {/if} 
    {if $rowName EQ 'avg'}
        {assign var = 'avg' value = $field }	
    {/if}
    {if $rowName EQ 'total'}
	{assign var = 'total' value = $field }  
    {/if}
    {/foreach}
    <tr>
    <td></td><td></td>
    </tr>
    <tr>
      <td>
      	Avg
      </td> 
      <td>
       {$avg|crmMoney}
      </td>
    </tr>
    <tr>
      <td>
      	Total
      </td> 
      <td>
       {$total|crmMoney}
      </td>
    </tr>
    <tr>
      <td><div><a href={$mAnti}>More Details</a></div></td>
      <td></td>
    </tr>
    </table>

    <p class=subtitle><b>Year to Date Donations</b></p>
    <table style = "width:250px">
    {foreach from=$thisYear item=field key=rowName}
    { if $rowName NEQ 'total' AND $rowName NEQ 'avg'}
    <tr>
      <td>
       {$rowName}
      </td> 
      <td>
       {$field|crmMoney}
      </td>
    </tr>  
    {/if} 
    {if $rowName EQ 'avg'}
       {assign var = 'avg' value = $field }	
    {/if}
    {if $rowName EQ 'total'}
       {assign var = 'total' value = $field }  
    {/if}
    {/foreach}
    <tr>
    <td></td><td></td>
    </tr>
    <tr>
      <td>
      	Avg
      </td> 
      <td>
       {$avg|crmMoney}
      </td>
    </tr>
    <tr>
      <td>
      	Total
      </td> 
      <td>
       {$total|crmMoney}
      </td>
    </tr>
    <tr>
      <td><div><a href={$ytd}>More Details</a></div></td>
      <td></td>
    </tr>
    </table>
</div>
{/foreach}
</div>