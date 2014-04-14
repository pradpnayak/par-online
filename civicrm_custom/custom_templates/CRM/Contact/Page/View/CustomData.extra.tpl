{*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2011                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*}
{if !empty($rows)}
<table>
  <thead>
    {foreach from=$columns item=column}
      <th>{$column}</th>
    {/foreach}
  </thead>
  <tbody>
    {foreach from=$rows item=row}
      <tr>	
        {foreach from=$columns item=column key=columnKey}
          <td>  
	    {if $column eq 'Changed By'}
              <a href='/civicrm/contact/view?reset=1&cid={$row.contact_ref_id}'>
            {/if}
            {$row.$columnKey}
            {if $column eq 'Changed By'}
              </a>
            {/if}
          </td>
        {/foreach}
      </tr>
    {/foreach}
  </tbody>
</table>
{else}
  <div class="messages status">	
    <div class="icon inform-icon"></div> &nbsp;
    No modifications have been logged for this contact.     
  </div>
{/if}