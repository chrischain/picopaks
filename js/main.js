/*
*  Application: PicoBrew PicoPaks
*  Filename: main.js
*  $Revision: 1.1.0 $
*  $LastChangedBy: Chris Chain $
*  $LastChangedDate: 01/16/18 $
*  Description: This is the main JavaScript file that configures the grid and data connector
*/

// Main Application Entry Point
$(document).ready(function () {
    // define main variables
    var dataAdapter, grid = $('#grid'), initrowdetails, source, toggleArray = [];

    /* data connection */
    // main datasource
    source = {
        datatype: 'json',
        datafields: [
            { name: 'Name' },
            { name: 'Brewery' },
            { name: 'Style' },
            { name: 'Description' },
            { name: 'ABV', type: 'number' },
            { name: 'IBU', type: 'int' },
            { name: 'SRM', type: 'int' },
            { name: 'Grains' },
            { name: 'Hops' },
            { name: 'Price' },
            { name: 'Rating' },
            { name: 'Reviews', type: 'int' },
            { name: 'URL' },
            { name: 'Updated' }
        ],
        id: 'id',
        url: 'data/data.php',
        root: 'Rows',
        beforeprocessing: function(data) {
          if (data != null) {
            source.totalrecords = data[0].TotalRows;

            // set the freshness date
            $('div#updated').html('Updated: ' + data[0].Rows[0].Updated);
          }
        }
    };

    // data renderer
    dataAdapter = new $.jqx.dataAdapter(source, {
      loadError: function(xhr, status, error) {
        alert(error);
      }
    });

    // row details renderer
    initrowdetails = function (index, parentElement, gridElement, datarecord) {
        var description, detailDiv = $($(parentElement).children()[0]), gobutton, grains, hops, information, leftcolumn, rightcolumn;

        if (detailDiv != null) {
            // set up the target and content containers
            information = detailDiv.find('.information');
            leftcolumn = $('<div style="float: left; width: 60%;"></div>');
            rightcolumn = $('<div style="float: left; width: 40%;"></div>');

            // push the containers into the target element
            information.append(leftcolumn);
            information.append(rightcolumn);

            /* now set up the content */
            // left column
            description = "<div style='margin: 10px; word-wrap: break-word; white-space: pre-wrap'><b>Description:</b> " + datarecord.Description + "</div>";

            // right column
            grains = "<div style='margin: 10px; word-wrap: break-word; white-space: pre-wrap'><b>Grains:</b> " + datarecord.Grains + "</div>";
            hops = "<div style='margin: 10px; word-wrap: break-word; white-space: pre-wrap'><b>Hops:</b> " + datarecord.Hops + "</div>";
            gobutton = "<div style='margin: 10px;'><a class='goto' target='_blank' href='" + datarecord.URL + "'>Open in BrewMarketplace</a></div>";

            /* and append them to their appropriate columns */
            // left column
            leftcolumn.append(description);

            // right column
            // only append these columns if they have data in them
            if (datarecord.Grains.length) {
              rightcolumn.append(grains);
            }
            if (datarecord.Hops.length) {
              rightcolumn.append(hops);
            }

            // always append the link button
            rightcolumn.append(gobutton);

            // give the link some style
            $('a.goto').jqxLinkButton({
              theme: 'dark',
              height: 30,
              width: 200
            });
        }
    }

    /* main grid setup */
    grid.jqxGrid({
      autoheight: true,
      width: '98%',
      autoloadstate: true,
      autosavestate: true,
      filterable: true,
      groupable: true,
      initrowdetails: initrowdetails,
      sortable: true,
      pageable: true,
      pagesize: 20,
      pagesizeoptions: ['10', '20', '50', '100'],
      rowdetails: true,
      rowdetailstemplate: { rowdetails: "<div style='margin: 10px;'><div class='information'></div></div>", rowdetailsheight: 200 },
      rendergridrows: function(obj) {
         return obj.data;
      },
      source: dataAdapter,
      theme: 'dark',
      columnsreorder: true,
      columnsresize: true,
      columns: [
        { text: 'Name', dataField: 'Name', minwidth: 220 },
        { text: 'Brewery', dataField: 'Brewery', width: 220 },
        { text: 'Style', dataField: 'Style', minwidth: 200 },
        { text: 'ABV', dataField: 'ABV', cellsalign: 'center', width: 70, cellsformat: 'p' },
        { text: 'IBU', dataField: 'IBU', cellsalign: 'center', width: 70 },
        { text: 'SRM', dataField: 'SRM', cellsalign: 'center', width: 70 },
        { text: 'Price', dataField: 'Price', width: 90, cellsformat: 'c2' },
        { text: 'Rating', dataField: 'Rating', width: 60 },
        { text: 'Reviews', dataField: 'Reviews', cellsalign: 'center', width: 70 }
      ]
    });

    // track rowdetails state for rowdoubleclick toggle
    for (var i = 0; i < dataAdapter.pagesize; i += 1) {
      toggleArray.push(false);
    }

    // doubleclick event handler
    grid.on('rowdoubleclick', function (event) {
      var index = event.args.rowindex;

      // toggle rowdetails with doubleclick
      if (toggleArray[index] == true) {
        grid.jqxGrid('hiderowdetails', index);
        toggleArray[index] = false;
      } else {
        grid.jqxGrid('showrowdetails', index);
        toggleArray[index] = true;
      }
    });

    // download Excel button
    $('#download').jqxButton({
      height: 26,
      width: 80,
      theme: 'dark'
    }).click(function() {
      window.open('PicoBrew_Beer_List.xlsx');
    });
});
