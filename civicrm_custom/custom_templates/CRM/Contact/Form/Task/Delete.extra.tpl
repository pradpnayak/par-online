{if $nsfTrueContacts}
  <div id = 'nsfTrueContacts'> </br>
    {ts}{$nsfTrueContacts} of the {$totalSelectedContacts} of Donors will not be deleted because their NSF flag is set to true.{/ts}
  </div>
  {literal}
  <script type="text/javascript">
    cj(document).ready(function(){
      cj('.crm-contact-task-delete-form-block div.messages').append(cj('#nsfTrueContacts').html());
      cj('#nsfTrueContacts').remove();
    });

  </script>
  {/literal}
{/if}