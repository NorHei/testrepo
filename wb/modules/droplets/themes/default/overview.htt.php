<?php
?>
<!DOCTYPE HTML>
<html lang="de">
<head>
  <meta charset="utf-8" />
  <title>SimplescrollTable</title>
  <meta name="description" content="tmp" />
  <meta name="keywords" content="tmp" />
  <meta name="revisit-after" content="7" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <!-- stylesheets -->
  <link href="scrollTable.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="tableContainer" id="tableContainer">
<table class="scrollTable" >
<thead class="fixedHeader">
    <tr class="">
        <th>
            <label>
                <input name="select_all" id="select_all" type="checkbox" value="1"  />
            </label>
        </th>
        <th></th>
        <th></th>
        <th class="sortierbar">{TITLE]</th>
        <th class="sortierbar">{DESCRIPTION}</th>
        <th class="sortiere-">{MODIFIED_WHEN}</th>
        <th></th>
        <th class="sortierbar" >{ACTIVE}</th>
        <th></th>
    </tr>
</thead>
<tfoot class="fixedFooter">
    <tr class="">
        <td colspan="9">Legende</td>
    </tr>
</tfoot>
<tbody class="scrollContent">

    <tr class="">
        <td>
                       <input type="checkbox" name="cb[{DropletId}]" id="L{DropletId}cb" value="{DropletName}" />
        </td>
        <td >
            <button name="command" type="submit" class="noButton" value="copy_droplet?droplet_id={iDropletIdKey}" title="">
                <img src="{sAddonThemeUrl}/img/copy_24.png" alt="" />
            </button>
        </td>
            <td style="cursor: pointer;">
                <button name="command" type="submit" class="noButton" value="modify_droplet?droplet_id={iDropletIdKey}" title="">
                    <img src="{sAddonThemeUrl}/img/modify_24.png" alt="" />
                </button>
            </td>
            <td style="cursor: pointer;">
                <button name="command" type="submit" class="noButton" value="modify_droplet?droplet_id={iDropletIdKey}">
                     <img src="{sAddonThemeUrl}/img/{icon}_24.png" alt=""/>
                </button>
            </td>
            <td >
                <button  class=" noButton" name="command" type="submit" class="noButton" value="modify_droplet?droplet_id={iDropletIdKey}">
                    {sDropletName}
                <span id="tooltip_{DropletId}">{comments}</span></button>
            </td>
        <td>
                {sDropletDescription}
            </td>
        <td>Cell Content 1</td>
        <td>Cell Content 2</td>
        <td>Cell Content 3</td>
    </tr>

    <tr class="">
        <td>End of Cell Content 1</td>
        <td>End of Cell Content 2</td>
        <td>End of Cell Content 3</td>
        <td>End of Cell Content 1</td>
        <td>End of Cell Content 2</td>
        <td>End of Cell Content 3</td>
        <td>End of Cell Content 1</td>
        <td>End of Cell Content 2</td>
        <td>End of Cell Content 3</td>
    </tr>
</tbody>
</table>
</div>
</body>
</html>
