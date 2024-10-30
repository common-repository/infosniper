/**
 * @file infosniper-admin.js
 * 
 * This file contains the javascript needed to run the infosniper page in 
 * wp-admin.
 */
ajax_called = [];

// This section of the code loads jQuery and contains any code that needs jquery
// to function.
( function ( $ ) {
  'use strict'; //Use strict javascript to avoid errors.

  $( document ).ready(function () {
    var hash, totalRows, recordPerPage, totalPages, $pages;

    // 1. Set up user logs and related functions.

    //Hide all extended info by default.
    $( '.user-info-extended' ).hide();
    //Onclick function for each log to display the extended information.
    $( '.user-info-basic' ).click( function () {
      var element = $( this ).parent().children( ":nth-child(2)" ),
        index, lati, long, layer, flag, type, marker, ip, time, provider, city,
        state, country, hour;

      //Load extra information
      var data = {
        'action': 'load_data',
        'whatever': 1234
      };

      index = element.data( 'user-log-index' );

      if( typeof( ajax_called[ index ] ) === 'undefined' ){
        $.ajax( {
          url      : ajaxurl,
          type     : 'POST',
          data     : {
            action : 'load_data',
            ipAddress : $( "#ip-" + index ).text().trim(),
          },
          dataType : 'json',

          //Error Handling
          error : function( MLHttpRequest, textStatus, errorThrown ) {
            console.error( errorThrown );
          },

          //Success Handling
          success : function( response, textStatus, XMLHttpRequest ) {
            //console.log( response );
            $( "#user-info-" + index ).children( ".info-sniper-content-log").append( 
              '<div class="info-sniper-content-group">' +
              '<div class="info-sniper-content-title">Hostname</div>' +
              '<div id="host-' + index + '"></div></div>' );
            $( "#host-" + index  ).html( response.hostname );
            $( "#user-info-" + index ).children( ".info-sniper-content-log").append( 
              '<div class="info-sniper-content-group">' +
              '<div class="info-sniper-content-title">Area Code</div>' +
              '<div id="area-' + index + '"></div></div>' );
            $( "#area-" + index  ).html( response.areacode );
            $( "#user-info-" + index ).children( ".info-sniper-content-log").append( 
              '<div class="info-sniper-content-group">' +
              '<div class="info-sniper-content-title">Postal Code</div>' +
              '<div id="post-' + index + '"></div></div>' );
            $( "#post-" + index  ).html( response.postalcode );
            $( "#user-info-" + index ).children( ".info-sniper-content-log").append( 
              '<div class="info-sniper-content-group">' +
              '<div class="info-sniper-content-title">DMA Code</div>' +
              '<div id="dma-' + index + '"></div></div>' );
            $( "#dma-" + index   ).html( response.dmacode );
            $( "#user-info-" + index ).children( ".info-sniper-content-log").append( 
              '<div class="info-sniper-content-group">' +
              '<div class="info-sniper-content-title">Timezone</div>' +
              '<div id="zone-' + index + '"></div></div>' );
            $( "#zone-" + index  ).html( response.timezone );
            $( "#user-info-" + index ).children( ".info-sniper-content-log").append( 
              '<div class="info-sniper-content-group">' +
              '<div class="info-sniper-content-title">GMT Off-Set</div>' +
              '<div id="gmt-' + index + '"></div></div>' );
            $( "#gmt-" + index   ).html( response.gmtoffset );
            $( "#user-info-" + index ).children( ".info-sniper-content-log").append( 
              '<div class="info-sniper-content-group">' +
              '<div class="info-sniper-content-title">Continent</div>' +
              '<div id="cont-' + index + '"></div></div>' );
            $( "#cont-" + index  ).html( response.continent );
            $( "#user-info-" + index ).children( ".info-sniper-content-log").append( 
              '<div class="info-sniper-content-group">' +
              '<div class="info-sniper-content-title">First Visit Date</div>' +
              '<div id="time-' + index + '"></div></div>' );
            $( "#time-" + index  ).html( response.firstDate );
            $( "#user-info-" + index ).children( ".info-sniper-content-log").append( 
              '<div class="info-sniper-content-group">' +
              '<div class="info-sniper-content-title">First Visit Time</div>' +
              '<div id="hour-' + index + '"></div></div>' );
            $( "#hour-" + index  ).html( response.firstTime );
            $( "#user-info-" + index ).children( ".info-sniper-content-log").append( 
              '<div class="info-sniper-content-group">' +
              '<div class="info-sniper-content-title">First Page Visited</div>' +
              '<div id="fpage-' + index + '"></div></div>' );
            $( "#fpage-" + index ).html( response.firstPage);
            ajax_called[ index ] = true;
          },
        } );
      }


      if ( element.is( ":hidden" ) ) {
        //Show case.
        index = element.data( 'user-log-index' );
        //Hide any currently used extended info.
        $( ".user-info-extended:visible" ).hide();
        //Remove any active maps.
        if ( myLogMapIndex !== index && typeof( myLogMap ) !== 'undefined' &&
          ! myLogMapRemoved ) myLogMap.remove();
        //Show this extended info.
        element.show();
        //Grab coordinates from log.
        lati = $( "#lati-" + index ).text().trim();
        long = $( "#long-" + index ).text().trim();
        //Init leaflet map.
        myLogMap = L.map( 'mapid-' + index ).setView( [ lati, long ], 13 );
        myLogMapIndex = index;
        //Map is currently not removed.
        myLogMapRemoved = 0;
        //Add stamen tile layer.
        layer = new L.stamenTileLayer( maptype );
        myLogMap.addLayer( layer );
        //Add marker to map.
        marker = L.marker( [ lati, long ] ).addTo( myLogMap );
        //Get relevant info from data
        flag     = $( "#flag-" + index ).html();
        type     = $( "#type-" + index ).text();
        ip       = $( "#ip-"   + index ).text();
        time     = $( "#ltime-" + index ).text();
        hour     = $( "#lhour-" + index ).text();
        provider = $( "#pro-"  + index ).text();
        city     = $( "#city-" + index ).text();
        state    = $( "#stat-" + index ).text();
        country  = $( "#coun-" + index ).text();
        //Add to map and open it.
        marker.bindPopup(
          flag + '<u>' + type + '</u>' +
          '<br>' +
          'IP: ' + ip +
          '<br>' +
          'Last Visit: ' + time + " @ " + hour +
          '<br>' +
          'ISP: ' + provider +
          '<br>' +
          city + ', ' + state + ', ' + country
        ).openPopup();
      } else {
        //Hide case.
        element.hide();
        myLogMap.remove();
        myLogMapRemoved = 1;
      }
    });

    //Create pages for new user logs.
    totalRows = ipCount;
    recordPerPage = 20;
    totalPages = Math.ceil( totalRows / recordPerPage );
    $pages = $('<div id="pages"></div>');
    //Create page numbers at bottom of user logs.
    for ( var i = 0; i < totalPages; i++ ) {
      $( '<span class="pageNumber">' + ( i + 1 ) + '</span>' ).appendTo( $pages );
    }
    $pages.appendTo( '#span-destination' );
    //Add hover state for styling purposes.
    $( '.pageNumber' ).hover( function () {
      $( this ).addClass( 'focus' );
    }, function () {
      $( this ).removeClass( 'focus' );
    });

    //Hide all entries by default
    $( '.user-log' ).hide();

    //Store logs in jquery variable.
    var logs = $( '.user-log' );

    //Show first page of logs.
    for ( var i = 0; i <= recordPerPage - 1; i++ ) {
      $( logs[ i ] ).show();
    }

    //Pagenumber onclick function.
    $( '.pageNumber' ).click( function( event ) {
      //Hide all currently visible logs
      $( ".user-info-extended:visible" ).hide();
      $( '.user-log' ).hide();
      //Changed selected pageNumber.
      $( '.pageNumber' ).removeClass( 'selected-page' );
      $( this ).addClass( 'selected-page' );
      //Set current page
      currentLogPage = parseInt( $( this ).text() );
      //Dsiplay pages.
      displayCurrentPages();
    });

    $( '#left-full') .click( function( event ) {
      //Set current page to start.
      currentLogPage = 1;
      //Change selected pageNumber.
      $( '.pageNumber' ).removeClass( 'selected-page' );
      $( '.pageNumber:contains("' + currentLogPage + '")' )
        .first()
        .addClass( 'selected-page' );
      //Display pages.
      displayCurrentPages();
    });

    $( '#left-arrow' ).click( function( event ) {
      //Decrement page number if able.
      if( currentLogPage ===  1 ) {
        return;
      }
      currentLogPage--;
      //Changed selected pageNumber.
      $( '.pageNumber' ).removeClass( 'selected-page' );
      $( '.pageNumber:contains("' + currentLogPage + '")' )
        .first()
        .addClass( 'selected-page' );
      //Display pages.
      displayCurrentPages();
    });

    $( '#right-arrow' ).click( function( event ) {
      //Increment page numer if able.
      if( currentLogPage === totalPages ) {
        return;
      }
      currentLogPage++;
      //Change selected pageNumber.
      $( '.pageNumber' ).removeClass( 'selected-page' );
      $( '.pageNumber:contains("' + currentLogPage + '")' )
        .first()
        .addClass( 'selected-page' );
      //Display pages.
      displayCurrentPages();
    });

    $( '#right-full' ).click( function( event ) {
      //Set page to final page.
      currentLogPage = totalPages;
      //Change selected PageNumber.
      $( '.pageNumber' ).removeClass( 'selected-page' );
      $( '.pageNumber:contains("' + currentLogPage + '")' )
        .first()
        .addClass( 'selected-page' );
      //Display Pages
      displayCurrentPages();
    });

    function displayCurrentPages() {
      var nBegin, nEnd;
      //Hide all current logs.
      $( ".user-info-extended:visible" ).hide();
      $( '.user-log' ).hide();
      //Determine start and ending point.
      nBegin = ( currentLogPage - 1 ) * recordPerPage;
      nEnd = ( currentLogPage ) * recordPerPage - 1;
      //Show all pages between start and ending points.
      for ( var i = nBegin; i <= nEnd; i++ ) {
        $( logs[ i ] ).show();
      }
      //Format pageNumber Menu.
      formatPageNumbers();
      //Scroll the window to top of section. Fixes a weird firefox issue.
      if( window.location.hash.includes( 'logs' ) ) {
        //If condition prevents scroll from happening on other pages on load.
        window.scroll({
          top: document.getElementById("infosniper-header").clientHeight + 
               document.getElementById("info-sniper-tabs").clientHeight + 
               document.getElementById("dashboard-buttons-2").clientHeight + 
               10, //10 is upper padding amount.
          left: 0,
          behavior: 'smooth'
        });
      }
    }

    function formatPageNumbers() {
      //Remove any current ellipsis.
      $( '.span-ellipsis' ).remove();
      //Hide all page numbers.
      $( '.pageNumber' ).hide();
      //Display first two page numbers.
      $( '.pageNumber' ).slice( 0, 2 ).show();
      //Display last two page numbers.
      $( '.pageNumber' ).slice( totalPages - 2 ).show();
      //Display two page numbers on either side of currently selected number.
      $( '.pageNumber' ).slice( Math.max( 0, currentLogPage - 3) , currentLogPage + 2 ).show();
      //If the third index is hidden we add an ellipsis.
      if( $( '.pageNumber:eq("2")' ).is( ":hidden" ) ) {
        $( '.pageNumber:eq("2")' ).after( '<span class="span-ellipsis">...</span>' );
      }
      //If the third from last index is hidden we add an ellipsis.
      if( $( '.pageNumber:eq("' + ( totalPages - 3 ) + '")' )
        .is( ":hidden" ) ) {
        $( '.pageNumber:eq("' + ( totalPages - 3 ) + '")' )
          .before( '<span class="span-ellipsis">...</span>' );
      }

    }

    /**
     * 2. Creates onclick functions for the tabs in the settings page so we can
     *    change between them.
     */
    $( '.info-sniper-tab' ).click( function () {
      var tabName, tabHTML;
      if ( $( this ).attr( 'id' ) === 'credits-tab' ) {
        return;
      }
      $( '#info-sniper-tabs' )
        .find( 'a' )
        .removeClass( 'info-sniper-tab-active' ),
        $( '.info-sniper-tab-content' ).removeClass( 'info-sniper-active' );
      tabName = $( this ).attr( "id" ).replace( "-tab", "" );
      tabHTML = $( '#' + tabName );
      tabHTML.addClass( "info-sniper-active" );
      $( this ).addClass( 'info-sniper-tab-active' );
      //Have to do this to avoid ellipsis.
      if ( $( this ).attr( 'id' ) === 'logs-tab' ) {
        displayCurrentPages();
      }
    });

    /**
     * 3. Move to appropriate tab on page load based on url hash.
     */
    hash = window.location.hash;
    if ( hash.includes( "settings" ) ) {
      $( "#settings-tab" ).click();
    } else if ( hash.includes( "dashboard" ) ) {
      $( "#dashboard-tab" ).click();
    } else if ( hash.includes( "logs" ) ) {
      $( "#logs-tab" ).click();
    } else if ( hash.includes( "help" ) ) {
      $( "#help-tab" ).click();
    }

    /**
     * 4. Add a change function to cause the preview image for tiles in the 
     *    settings menu to change.
     */
    //Hide the initial preview.
    $( '.infosniper-tile-preview' ).hide();
    //Show the preview of the current saved option.
    $( '#' + $( '#infosniper-map-type-selector' ).val() + "-preview" ).show();
    // Create function to control future changes.
    $( '#infosniper-map-type-selector' ).change( function () {
      $( '.infosniper-tile-preview' ).hide();
      $( '#' + $( this ).val() + '-preview' ).show();
    })

    function displayLogClear() {
      if( $( "#log-clear-wrap" ).is( ":hidden" ) ) {
        $( "#log-clear-wrap" ).show();
      } else {
        $( "#log-clear-wrap" ).hide();
      }
    }
    $( "#log-clear-wrap" ).hide();

    $( '.clear-wrap-toggle' ).click( displayLogClear );

    //Click first page number to set initial state.
    $( '.pageNumber:eq("0")' ).click();

    /**
     * 5. Add ajax for query testing.
     */
    $( '#key-check-btn' ).click( function() {
      $.ajax( {
        url      : ajaxurl,
        type     : 'POST',
        data     : {
          action : 'validate_key',
          key    : $( "#infosniper-key" ).val()
        },
        dataType : 'json',

        //Error Handling
        error : function( MLHttpRequest, textStatus, errorThrown ) {
          console.error( errorThrown );
        },

        //Success Handling
        success : function( response, textStatus, XMLHttpRequest ) {
          //console.log( response );
          $( '#key-check' ).hide();
          if( response.valid ){
            $( '#key-valid' ).show();
            $( "#infosniper-queries-info" ).html( "Credits Remaining: <strong>" + numberWithCommas( response.queries ) + "<strong>" );
          } else {
            $( '#key-invalid').show();
          }
        },
      } );
    });

    $( '#dashboard-count' ).change( function() {
      var selected = $( '#dashboard-count option:selected' ).text().trim();
      if( selected === 'Last Week'  || 
          selected === 'Last Month' ||
          selected === 'Last Six Months' || 
          selected === 'Last Year' ){
        var msg = "Loading large amounts of logs may cause longer loading times on this page. Are you sure you want to load more logs?";
        if( confirm( msg ) ){
          $( '#info-sniper-dashboard-dropdown' ).submit();
        }
      } else {
        $( '#info-sniper-dashboard-dropdown' ).submit();
      }
    });

    $( '#logs-count' ).change( function() {
      var selected = $( '#logs-count option:selected' ).text().trim();
      if( selected === 'Last Week'  || 
          selected === 'Last Month' ||
          selected === 'Last Six Months' || 
          selected === 'Last Year' ){
        var msg = "Loading large amounts of logs may cause longer loading times on this page. Are you sure you want to load more logs?";
        if( confirm( msg ) ){
          $( '#info-sniper-logs-dropdown' ).submit();
        }
      } else {
        $( '#info-sniper-logs-dropdown' ).submit();
      }
    });

    $( '#options-count' ).change( function() {
      var selected = $( '#options-count option:selected' ).text().trim();
      if( selected === 'Last Week'  || 
          selected === 'Last Month' ||
          selected === 'Last Six Months' || 
          selected === 'Last Year' ){
        var msg = "Loading large amounts of logs may cause longer loading times on this page.";
        alert( msg );
      }
    });

    $( '#dashboard-date' ).change( function() {
      var msg = "Loading large amounts of logs may cause longer loading times on this page. Are you sure you want to load more logs?";
      if( confirm( msg ) ){
        $( '#info-sniper-dashboard-date' ).submit();
      }
    });

    $( '#logs-date' ).change( function() {
      var msg = "Loading large amounts of logs may cause longer loading times on this page. Are you sure you want to load more logs?";
      if( confirm( msg ) ){
        $( '#info-sniper-logs-date' ).submit();
      }
    });

  });
})(jQuery);

/**
 * This is an class the we build the rest of our geographic class's our.
 * It represents a place and contains a name, an amount of users, and an array
 * containing coordinate pairs of users in the region.
 * The bounds array is used for changing the map view.
 */
class Geo {
  constructor( name, users ) {
    this.name = name;
    this.users = users;
    this.bounds = [];
  }
}

/**
 * Create a country class that contains all of geo's fields but also allows us
 * to track an array of children states.
 */
class Country extends Geo {
  constructor( name, users ) {
    super( name, users );
    this.states = [];
  }
}

/**
 * Create a state class that contains all of geo's fields but also allows us to
 * know the name of the parent country and to track an array of children cities.
 */
class State extends Geo {
  constructor( name, users, country ) {
    super( name, users );
    this.country = country;
    this.cities = [];
  }
}

/**
 * Create a city class that contains all of geo's fields but also allows us to
 * know the name of the parent country and parent state.
 */
class City extends Geo {
  constructor( name, users, country, state ) {
    super( name, users );
    this.country = country;
    this.state = state;
  }
}

/**
 * Takes the amount of entries in the user logs and then loops through them to
 * create an array of country objects, each of which contain states and cities.
 * 
 * @param {int} count The amount of entries in the user logs.
 */
function generateCountryArray(count) {
  //Loop from 0 to count in order to hit every entry in the user logs.
  for ( var i = 0; i < count; i++ ) {
    //Pull the necessary data from the logs at the start of the iteration.
    var country = document.getElementById( "coun-" + i ).textContent.trim(),
      state = document.getElementById( "stat-" + i ).textContent.trim(),
      city = document.getElementById( "city-" + i ).textContent.trim(),
      type = document.getElementById( "type-" + i ).textContent.trim(),
      lati = parseFloat( document.getElementById( "lati-" + i ).textContent.trim() ),
      long = parseFloat( document.getElementById( "long-" + i ).textContent.trim() );
    //We only want to track users in this array so continue if not a user.
    if ( type != "User" ) continue;
    //Dont map if we dont know a good approximation of the location.
    if ( country === "n/a" || country === "" ) continue;
    if ( state === "n/a" || state === "" ) continue;
    if ( city === "n/a" || city === "" ) continue;

    //If we dont have the current country yet we create a new country object.
    //Otherwise we just increment the amount of users in the current object.
    if ( ! countries[country] ) {
      countries[country] = new Country(country, 1);
    } else {
      countries[country].users++;
    }
    //Add coordinate info to the bounds.
    countries[country].bounds.push( [ lati, long ] );

    //If we dont have the current state yet we create a new state object.
    //Otherwise we just increment the amount of users in the current object.
    if ( ! countries[country].states[state] ) {
      countries[country].states[state] = new State(state, 1, country);
    } else {
      countries[country].states[state].users++;
    }
    //Add coordinate info to the bounds.
    countries[country].states[state].bounds.push( [ lati, long ] );

    //If we dont have the current city yet we create a new city object.
    //Otherwise we just increment the amount of users in the current object.
    if ( ! countries[country].states[state].cities[city] ) {
      countries[country].states[state].cities[city] = new City(city, 1, country, state);
    } else {
      countries[country].states[state].cities[city].users++;
    }
    //Add coordinate info to the bounds.
    countries[country].states[state].cities[city].bounds.push( [ lati, long ] );
  }
}

/**
 * Clears the geo-table of all data. Used on scope changes to make sure we can
 * hide whichever rows dont have data in them.
 */
function clearTable() {
  //Loop through all rows of the table and set them to empty string.
  for (var i = 0; i < 10; i++ ) {
    document.getElementById( "geo-" + i ).textContent = "";
    document.getElementById( "geo-users-" + i ).textContent = "";
  }
}

/**
 * Finds possible parent countries when given a string that is the name of a 
 * state.
 * 
 * @param {string} state 
 */
function findCountry( state ) {
  //We need the array because some countries have states with the same name.
  var parents = [];
  Object.keys( countries ).forEach( function ( country ) {
    if ( state in countries[country].states ) {
      parents.push( country );
    }
  });
  return parents;
}

/**
 * Finds possible parent countries and states when given a string that is the
 * name of a city.
 * 
 * @param {string} city 
 */
function findState( city ) {
  //We use the array in case there are any duplicate named country/state pairs.
  var parent, grandparent,
    parentPairs = [];
  Object.keys( countries ).forEach( function ( country ) {
    Object.keys( countries[country].states ).forEach( function ( state ) {
      if ( city in countries[country].states[state].cities ) {
        parent = state;
        grandparent = country;
        parentPairs.push( [ parent, grandparent ] );
      }
    });
  });
  return parentPairs;
}

/**
 * Generates a random rgb color code.
 */
function dynamicColor() {
  var r = Math.floor( Math.random() * 255 ),
    g = Math.floor( Math.random() * 255 ),
    b = Math.floor( Math.random() * 255 );
  return r + ',' + g + ',' + b;
}

/**
 * Pushes the same color into two arrays array1 having a lower opacity.
 * 
 * @param {array} array1 The array in which the lower opacity color will go.
 * @param {array} array2 The array in which the higher opacity color will go.
 */
function pushDynamicColor(array1, array2) {
  var color = dynamicColor();
  array1.push('rgba(' + color + ',0.2)');
  array2.push('rgba(' + color + ",1)");
}

/**
 * Sorts an array of geo objects into descending order by their users.
 * 
 * @param {array} geoArr 
 */
function sortByUsers(geoArr) {
  return Object
    .keys(geoArr)
    .sort(function (a, b) { return geoArr[a].users - geoArr[b].users })
    .reverse();
}

/**
 * Sets the legend of the geo-table and bar chart. By that we mean whether we
 * are showing countries, states, or cities as base. This shows one of the three
 * in descending order.
 * 
 * @param {string} legend The geotype by which we will be sorting.
 */
function setLegend(legend) {
  var sortedCountries, statesArray, sortedStates, citiesArray, sortedCities,
    indices,
    table_labels = [],
    table_data = [],
    table_backgrounds = [],
    table_borders = [],
    i = 0;
  //Clean up the geo-table before we begin.
  clearTable();
  //Sort our countries array.
  sortedCountries = sortByUsers(countries);

  //Create and fill an array of states using our countries array.
  statesArray = [];
  sortedCountries.forEach(function (country) {
    Object.keys(countries[country].states).forEach(function (state) {
      statesArray[state] = countries[country].states[state];
    });
  });
  //Sort our states array.
  sortedStates = sortByUsers(statesArray);

  //Create and fill an array of cities using countries array again.
  citiesArray = [];
  sortedCountries.forEach(function (country) {
    Object.keys(countries[country].states).forEach(function (state) {
      Object
        .keys(countries[country].states[state].cities)
        .forEach(function (city) {
          citiesArray[city] = countries[country].states[state].cities[city];
        });
    })
  });
  //Sort our cities array.
  sortedCities = sortByUsers(citiesArray);

  //Use our switch statement to decide which array to display the data from.
  switch (legend) {
    case "country":
      //Loop through our sorted countries
      sortedCountries.forEach(function (country) {
        //Set our currentCountry
        var currentCountry = countries[country];

        //Push the data into the appropriate arrays.
        table_labels.push(currentCountry.name);
        table_data.push(currentCountry.users);
        //Push a random color into the appropriate arrays.
        pushDynamicColor(table_backgrounds, table_borders);
        //Update the table with the appropriate info
        i = updateGeoTable(i, currentCountry);

      });
      //Clean and hide any unused rows. 
      cleanGeoTable(i);
      break;
    case "state":
      //We use indices array to handle any duplicate states.
      indices = [];
      //Loop through our sorted states array.
      sortedStates.forEach(function (state) {
        var sortedParentCountries, currentState,
          //Find parent countries of the current state.
          parentCountries = findCountry(state);
        //If the current indices is undefined we set initialize it to 0.
        if (indices[state] === undefined) {
          indices[state] = 0;
        }
        //Sort our array of possible parents.
        sortedParentCountries = [];
        parentCountries.forEach(function (country) {
          sortedParentCountries[country] = countries[country];
        });
        sortedParentCountries = sortByUsers(sortedParentCountries);
        //Take the our state from our array of possible parents by our current
        //indicies and then increment.
        currentState = countries[sortedParentCountries[indices[state]]].states[state];
        indices[state]++;

        //Push the data int othe appropriate arrays.
        table_labels.push(currentState.name);
        table_data.push(currentState.users);
        //Push a random color into the appropriate arrays.
        pushDynamicColor(table_backgrounds, table_borders);
        //Update the table with the appropriate info
        i = updateGeoTable(i, currentState);
      });
      //Clean and hide any unused rows.
      cleanGeoTable(i);
      break;
    case "city":
      //We use indices array to handle any duplicate states.
      indices = [];
      //Loop through our sorted cities array.
      sortedCities.forEach(function (city) {
        var sortedParents, parentState, parentCountry, currentCity,
          //Find parents of our current city.
          parents = findState(city);
        //If we dont have an indices for our city initialize it to 0.
        if (indices[city] === undefined) {
          indices[city] = 0;
        }
        //Create our sortedParents array from out parents array.
        sortedParents = [];
        parents.forEach(function (parentArray) {
          var state = parentArray[0],
            country = parentArray[1];
          sortedParents[state + "," + country] = countries[country].states[state];
        });
        //Sort our sortedParrents array by users.
        sortedParents = sortByUsers(sortedParents);
        //Select our parents from sortedParents based on our current index.
        //Then increment it.
        parents = sortedParents[indices[city]].split(',');
        indices[city]++;
        //Unpack parents into individual values for state and country.
        parentState = parents[0];
        parentCountry = parents[1];
        //Use those values to get our currentCity object.
        currentCity = countries[parentCountry].states[parentState].cities[city];
        //Push our data to the appropriate arrays.
        table_labels.push(currentCity.name);
        table_data.push(currentCity.users);
        //Push a random color to the appopriate arrays.
        pushDynamicColor(table_backgrounds, table_borders);
        //Update our table with the appropriate info.
        i = updateGeoTable(i, currentCity);
      });
      //Clean and hide any unused rows.
      cleanGeoTable(i);
      break;
  }
  //Update our chart with the data we have been collecting through the function.
  myChart.data.labels = table_labels.slice(0, 10);
  myChart.data.datasets[0].data = table_data.slice(0, 10);
  myChart.data.datasets[0].backgroundColor = table_backgrounds.slice(0, 10);
  myChart.data.datasets[0].borderColor = table_borders.slice(0, 10);
  myChart.update();
  //Update our global geotype variable.
  geotype = legend;
  //Capitalise the first letter of our variable to set as the header of the 
  //table.
  document
    .getElementById("table-head")
    .textContent = legend[0].toUpperCase() + legend.substr(1);
  //Only try to fit bounds if there are bounds to fit too.
  if (bounds.length != 0) {
    my_map.fitBounds(bounds);
  }
  //Empty our global values.
  globalCountry = "";
  globalState = "";
}

/**
 * Moves our chart down a geo-level. I.E. going from showing countries to 
 * showing states that were a child of the selected country.
 * 
 * @param {string} geo 
 * @param {int} users 
 */
function descend(geo, users) {
  var object, children, potentialCountries, currCountry, potentialParents,
    currState, geoBounds, i,
    table_labels = [],
    table_data = [],
    table_backgrounds = [],
    table_borders = [];
  //Use our switch statement to decide which logic to use.
  //geotype is a global scope variable defined on page.
  switch (geotype) {
    case "country":
      //Get our object using the parameter name.
      object = countries[geo];
      //Update our global variables.
      globalCountry = geo;
      globalCountryUsers = object.users;
      geotype = "state";
      //Grab the state array for use later.
      children = object.states;
      //Update the header of the geo table.
      document.getElementById("table-head").textContent = "State";
      break;
    case "state":
      //Find the name of our country using findCountry.
      potentialCountries = findCountry(geo);
      currCountry = "";
      //Compare the amount of users to make sure we take the right one.
      potentialCountries.forEach(function (country) {
        if (countries[country].states[geo].users === users) {
          currCountry = country;
        }
      });
      //Get our object using the parameter name.
      object = countries[currCountry].states[geo];
      //Update global variables.
      globalState = geo;
      globalStateUsers = object.users;
      geotype = "city";
      //Grab the city array for using later.
      children = object.cities;
      //Update the header of the geo table.
      document.getElementById("table-head").textContent = "City";
      break;
    case "city":
      //Find the name of country and state using findState.
      potentialParents = findState(geo);
      currState = "";
      currCountry = "";
      potentialParents.forEach(function (parentArray) {
        var state = parentArray[0],
          country = parentArray[1];
        if (countries[country].states[state].cities[geo].users = users) {
          currCountry = country;
          currState = state;
        }
      });
      //Grab our object using the parameter name.
      object = countries[currCountry].states[currState].cities[geo];
      //Fit the map view to the selected city.
      geoBounds = L.latLngBounds(object.bounds).pad(0.1);
      if (geoBounds.length != 0) {
        my_map.fitBounds(geoBounds);
      }
      //Return and ignore the rest of the logic.
      return;
  }
  //Clear the table so we can refill it with the new info.
  clearTable();
  i = 0;
  //Sort our array of children geo objects.
  sortedChildren = sortByUsers(children);
  //Loop through our sortedChildren.
  sortedChildren.forEach(function (name) {
    //Push data to the appropriate arrays.
    table_labels.push(children[name].name);
    table_data.push(children[name].users);
    //Push a random color to the appropriate arrays.
    pushDynamicColor(table_backgrounds, table_borders);
    //Update the geo-table with our new information.
    i = updateGeoTable(i, children[name]);
  });
  //Clean up the geo-table and hide an excess rows.
  cleanGeoTable(i);
  //Pad our map bounds so all the markers fit completely.
  var geoBounds = L.latLngBounds(object.bounds).pad(0.1);
  //Fit the map to bounds if we have bounds.
  if (geoBounds.length != 0) {
    my_map.fitBounds(geoBounds);
  }
  //Update our bar chart with the information we filled earlier.
  myChart.data.labels = table_labels.slice(0, 10);
  myChart.data.datasets[0].data = table_data.slice(0, 10);
  myChart.data.datasets[0].backgroundColor = table_backgrounds.slice(0, 10);
  myChart.data.datasets[0].borderColor = table_borders.slice(0, 10);
  myChart.update();
}

/**
 * Handles the clicks on our bar chart. If we click on a bar we use the
 * information stored in array to descend a level. Otherwise we move up a level.
 * 
 * @param {*} event event from chartjs.
 * @param {*} array Array provided by chartjs based on where you click.
 */
function handleClick(event, array) {
  var index, geo, users;
  //If there is no info in the array we are moving up a level.
  if (array.length === 0) {
    switch (geotype) {
      case "country":
        //If we are at the country level there is no way to go up.
        break;
      case "state":
        //For state we can just set the legend to country and show all the
        //countrys to ascend.
        setLegend('country');
        break;
      case "city":
        //If we have a global country set we can just descend from it to show
        //the appropriate states.
        //Otherwise just set the legend to state and show them all.
        if (globalCountry !== "") {
          geotype = 'country';
          descend(globalCountry, globalCountryUsers)
        } else {
          setLegend('state');
        }
        break;
    }
  }
  //If there is info in the array we want to descend.
  if (array[0]) {
    //Grab the index from the array.
    index = array[0]._index;
    //Use that to grab the label and data from the chart.
    geo = myChart.data.labels[index];
    users = myChart.data.datasets[0].data[index];
    //Descend using that info.
    descend(geo, users);
  }
}

/**
 * Handles clicks on the geo-table in the dashboard. Uses the information stored
 * there to descend on click.
 * 
 * @param {int} index index that allows us to know what info to grab from the 
 *                    table.
 */
function tableHandleClick(index) {
  //Grab information from the table.
  var geo = document.getElementById("geo-" + index).textContent.trim(),
    users = document.getElementById("geo-users-" + index).textContent.trim();
  //Descend a level.
  descend(geo, users);
}

/**
 * Initializes a chartjs bar chart with the options we need.
 * 
 * @param {*} ctx A canvas context that the chart will be drawn on.
 */
function createChart(ctx) {
  return new Chart(ctx, {
    type: 'bar',
    data: {
      labels: [],
      datasets: [
        {
          label: '# of Users',
          data: [],
          backgroundColor: [],
          borderColor: [],
          borderWidth: 1
        },
      ]
    },
    options: {
      onClick: handleClick,
      maintainAspectRatio: false,
      scales: {
        xAxes: [{
          maxBarThickness: 50
        }],
        yAxes: [{
          ticks: {
            beginAtZero: true
          }
        }]
      }
    }
  });
}

/**
 * Populates the page table using information from the user logs.
 */
function populatePageTable() {
  var type, page, sortedPages, users, text, i,
    pages = [];

  //Loop through the user logs.
  //ipCount is a global variable
  for (i = 0; i < ipCount; i++) {
    //Get the type from the user log and if it isnt User this row in the logs.
    type = document.getElementById('type-' + i).textContent.trim();
    if (type !== 'User') continue;
    //Grab the page information and cut the hyperlink at the end.
    page = document.getElementById('lpage-' + i).textContent.trim();
    page = page.substring(0, page.length - 2);
    //If we already have this page in our array add 1 to the user count.
    //Otherwise add it to the array.
    if (page in pages) {
      pages[page] += 1;
    } else {
      pages[page] = 1;
    }
  }
  //Sort our pages array by value in descending order.
  sortedPages = Object.keys(pages).sort(function (a, b) {
    return pages[b] - pages[a];
  });

  //Reset i to 0 so we can loop through the rows of page table.
  i = 0;
  sortedPages.forEach(function (page) {
    //For first 8 rows we record name and users.
    //Afterwards we group the remaining into an Other category.
    if (i < 9) {
      users = pages[page];
      document.getElementById('page-' + i).innerHTML = page.replace(/^.*\/\/[^\/]+/, '') + " <a href='JavaScript:newPopup(" + '"' + page + '"' + ");'>⧉</a> ";
      document.getElementById('page-users-' + i).innerHTML = users;
      i++;
    } else {
      users = pages[page];
      document.getElementById('page-' + i).innerHTML = "Other";
      if (document.getElementById('page-users-' + i).innerHTML === "") {
        document.getElementById('page-users-' + i).innerHTML = 0;
      }
      document.getElementById('page-users-' + i).innerHTML = Number(document.getElementById('page-users-' + i).textContent.trim()) + 1;
    }
  });
  //Hide any unused rows.
  while (i < 10) {
    text = document.getElementById('page-' + i).textContent.trim();
    if (text === '') {
      document.getElementById('page-row-' + i).style = "display: none;";
    }
    i++;
  }
}

/**
 * Initalizes and fills a leafletjs map with markers and circles according to 
 * data in the user logs. Also choose the tile provider based on maptype.
 * 
 * @param {L.Map}  map     Base map object previously made.
 * @param {string} maptype Type of tiles to display on the map.
 */
function createMap(map, maptype) {
  var country, type, accuracy, lati, long, state, city, flag, ip, provider,
    time, marker, meters,
    //Set the terrain layer according to the maptype.
    terrainLayer = new L.StamenTileLayer(maptype);
  //Add the terrain layer to the map.
  map.addLayer(terrainLayer);

  var progress = document.getElementById( 'progress' );
  var progressBar = document.getElementById( 'progress-bar' );

  function updateProgressBar( processed, total, elapsed, layersarray ){
    if (elapsed > 1000) {
      // if it takes more than a second to load, display the progress bar:
      progress.style.display = 'block';
      progressBar.style.width = Math.round(processed/total*100) + '%';
    }

    if (processed === total) {
      // all markers processed - hide the progress bar:
      progress.style.display = 'none';
    }
  }

  var markers = L.markerClusterGroup( { 
    chunkedLoading: true,
    chunkProgress: updateProgressBar
  } );

  var markerList = [];

  //Loop through the user logs.
  for (var i = 0; i < ipCount; i++) {
    //Pull relevant data.
    country = document.getElementById("coun-" + i).textContent.trim();
    type = document.getElementById("type-" + i).textContent.trim();
    //accuracy = document.getElementById("acc-" + i).textContent.trim();
    lati = parseFloat(document.getElementById("lati-" + i).textContent.trim());
    long = parseFloat(document.getElementById("long-" + i).textContent.trim());
    state = document.getElementById('stat-' + i).textContent.trim();
    city = document.getElementById('city-' + i).textContent.trim();
    flag = document.getElementById("flag-" + i).children[0].src;
    ip = document.getElementById('ip-' + i).textContent.trim();
    provider = document.getElementById('pro-' + i).textContent.trim();
    time = document.getElementById('ltime-' + i).textContent.trim();
    //If any of these conditions are true we ignore the row 
    //if( type === 'Bot' ) continue;
    if (country === "" || country === "n/a") continue;
    if (state === "" || state === "n/a") continue;
    if (city === "" || city === "n/a") continue;
    if (isNaN(lati) || isNaN(long)) continue;

    if (selectedUserType === "all" || selectedUserType === "") {
      //Do nothing.
    } else if (type.toLowerCase() !== selectedUserType) {
      continue;
    }

    //Use different icons for each type of user.
    switch (type) {
      case 'User':
        marker = L.marker([lati, long], { icon: userIcon });
        break;
      case 'Bot':
        marker = L.marker([lati, long], { icon: botIcon });
        break;
      case 'Potential Threat':
        marker = L.marker([lati, long], { icon: infilIcon });
        break;
      case 'Admin':
        marker = L.marker([lati, long], { icon: adminIcon });
        break;
    }

    //Add marker to map
    //marker.addTo( my_map );

    //Add marker to markerlist
    markerList.push( marker )

    //Bind a popup to the marker.
    marker.bindPopup(
      '<img src="' + flag + '"> <u>' + type + '</u>' +
      '<br>' +
      'IP: ' + ip +
      '<br>' +
      'Last Visit: ' + time +
      '<br>' +
      'ISP: ' + provider +
      '<br>' +
      city + ', ' + state + ', ' + country
    );
    //Add the coordinates to the bounds array.
    bounds.push([lati, long]);
    //We dont want to add a circle if either of these are true.
    //if (accuracy > 35 || type === 'Bot') continue;
    //Covert the miles accuracy unit to meters for leafletjs.
    //meters = accuracy * 1609.344;
    //Bind a circle to the map 
    //L.circle([lati, long], {
    //  color: 'red',
    //  fillColor: '#f03',
    //  fillOpacity: 0.2,
    //  radius: meters
    //}).addTo(my_map);
    // Deprecated as of 7/17/2020
  }

  markers.addLayers( markerList );
  map.addLayer( markers )

  //Fit the map to the bounds if we have bounds.
  if (bounds.length != 0) {
    bounds = L.latLngBounds(bounds).pad(0.1);
    map.fitBounds(bounds);
  }
}

/**
 * Opens a popup to the specified url.
 * 
 * @param {string} url 
 */
function newPopup(url) {
  popupWindow = window.open(
    url,
    'popUpWindow',
    'height=600,width=600,left=10,top=10,resizable=yes,scrollbars=yes,toolbar=yes,menubar=no,location=no,directories=no,status=yes'
  )
}

/**
 * Updates the geo-table with info from current-Geo at a row specified by i.
 * 
 * @param {int} i          Index of the row to be updated.
 * @param {Geo} currentGeo Geo object we are going to be pull data from.
 */
function updateGeoTable(i, currentGeo) {
  var userTotal;
  //Decide what logic we need based on our current index.
  if (i < 9) {
    //Lower indices just fill in their own information and increment.
    document.getElementById("geo-row-" + i).style = '';
    document.getElementById("geo-" + i).textContent = currentGeo.name;
    document.getElementById("geo-users-" + i).textContent = currentGeo.users;
    i++;
  } else {
    //At 9 we stop incrementing and begin to fill in the other category.
    document.getElementById("geo-row-" + i).style = '';
    document.getElementById("geo-" + i).textContent = "Other";
    //Pull current Other userTotal, if not set create a new one.
    if (document.getElementById("geo-users-" + i).textContent == "") {
      userTotal = 0;
    } else {
      userTotal = Number(document.getElementById("geo-users-" + i).textContent.trim());
    }
    //Add current users to the userTotal and update the table.
    userTotal += currentGeo.users;
    document.getElementById("geo-users-" + i).textContent = userTotal;
  }
  return i;
}

/**
 * Cleans the geo table starting at index i.
 * 
 * @param {int} i The index to start cleaning from.
 */
function cleanGeoTable(i) {
  var text;
  //Go through the table and hide any not in use.
  while (i < 10) {
    text = document.getElementById("geo-" + i).textContent.trim();
    if (text === "") {
      document.getElementById("geo-row-" + i).style = 'display: none;';
    }
    i++;
  }
}

/**
 * Toggles visibility of an input wih the given id.
 * 
 * @param {string} id 
 */
function keyVisibleToggle(id) {
  //Grab the HTML object of the given id.
  var key = document.getElementById( id ) ;
  //If its a password set it to text and if its not a password set it to
  //password.
  if (key.type === 'password') {
    key.type = 'text';
    document.getElementById('key-visible').style = "";
    document.getElementById('key-hidden').style = "display:none;";
  } else {
    key.type = 'password';
    document.getElementById('key-visible').style = "display:none;";
    document.getElementById('key-hidden').style = "";
  }
}

/**
 * Creates a new marker icon with the given png that is stored in 
 * path/css/images/
 * 
 * @param {string} png 
 * @param {string} path
 */
function createLeafletMarkerIcon(path, png) {
  //Just need to make sure iconUrl and shadowUrl are right because the rest
  //are constant due to consistent image sizes.
  return L.icon({
    iconUrl: path + '/css/images/' + png + '.png',
    shadowUrl: path + '/css/images/marker-shadow.png',

    iconSize: [25, 41],
    shadowSize: [41, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    tooltipAnchor: [16, -28],
  });
}

function numberWithCommas(x) {
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function csvExport() {
  var rowArray = [],
    header = [];

  header.push( 'IP Address' );
  header.push( 'Last Date Visited' );
  header.push( 'Last Time Visited' );
  header.push( 'Last Page Visited' );
  header.push( 'User Type' );
  header.push( 'Hostname' );
  header.push( 'Internet Provider' );
  header.push( 'Country' );
  header.push( 'Country Code' );
  header.push( 'State' );
  header.push( 'City' );
  header.push( 'Area Code' );
  header.push( 'Postal Code' );
  header.push( 'DMA Code' );
  header.push( 'Timezone' );
  header.push( 'GMT Offset' );
  header.push( 'Continent' );
  header.push( 'Latitude' );
  header.push( 'Longitude' );
  //header.push( 'Accuracy' );
  header.push( 'First Date Visited' );
  header.push( 'First Time Visited' );
  header.push( 'First Page Visited' );
  header.push( 'User Agent' );

  rowArray.push( header );
  for( var i = 0; i < ipCount; i++ ) {
    var row = [];
    row.push( document.getElementById( 'ip-' + i      ).textContent.trim().replace( ",", "" ) );
    row.push( document.getElementById( 'ltime-' + i   ).textContent.trim().replace( ",", "" ) );
    row.push( document.getElementById( 'lhour-' + i   ).textContent.trim().replace( ",", "" ) );
    row.push( document.getElementById( 'lpage-' + i   ).textContent.trim().replace( ",", "" ).slice( 0, -2 ) ); //Trim new window link.
    row.push( document.getElementById( 'type-' + i    ).textContent.trim().replace( ",", "" ) );
    row.push( document.getElementById( 'host-' + i    ).textContent.trim().replace( ",", "" ) );
    row.push( document.getElementById( 'pro-' + i     ).textContent.trim().replace( ",", "" ) );
    row.push( document.getElementById( 'coun-' + i    ).textContent.trim().replace( ",", "" ) );
    row.push( document.getElementById( 'code-' + i    ).textContent.trim().replace( ",", "" ) );
    row.push( document.getElementById( 'stat-' + i    ).textContent.trim().replace( ",", "" ) );
    row.push( document.getElementById( 'city-' + i    ).textContent.trim().replace( ",", "" ) );
    row.push( document.getElementById( 'area-' + i    ).textContent.trim().replace( ",", "" ) );
    row.push( document.getElementById( 'post-' + i    ).textContent.trim().replace( ",", "" ) );
    row.push( document.getElementById( 'dma-' + i     ).textContent.trim().replace( ",", "" ) );
    row.push( document.getElementById( 'zone-' + i    ).textContent.trim().replace( ",", "" ) );
    row.push( document.getElementById( 'gmt-' + i     ).textContent.trim().replace( ",", "" ) );
    row.push( document.getElementById( 'cont-' + i    ).textContent.trim().replace( ",", "" ) );
    row.push( document.getElementById( 'lati-' + i    ).textContent.trim().replace( ",", "" ) );
    row.push( document.getElementById( 'long-' + i    ).textContent.trim().replace( ",", "" ) );
    //row.push( document.getElementById( 'acc-' + i     ).textContent.trim().replace( ",", "" ) );
    row.push( document.getElementById( 'time-' + i    ).textContent.trim().replace( ",", "" ) );
    row.push( document.getElementById( 'hour-' + i    ).textContent.trim().replace( ",", "" ) );
    row.push( document.getElementById( 'fpage-' + i   ).textContent.trim().replace( ",", "" ) );
    row.push( document.getElementById( 'agen-' + i    ).textContent.trim().replace( ",", "" ) );

    rowArray.push( row );
  }

  var csvData = [];
  rowArray.forEach( function( row, index ) {
    var line = row.join(", ");
    csvData.push( index == 0 ? "data:text/csv;charset=utf-8," + line : line );
  });
  var csvContent = rowArray.join("\n");
  var csvBlob = new Blob( [ csvContent ], { type: 'text/csv;charset=utf-8' } );
  var csvDownloadLink = document.createElement( 'a' );
  var csvUrl = URL.createObjectURL( csvBlob );
  csvDownloadLink.setAttribute( "href", csvUrl );
  csvDownloadLink.setAttribute( "download", "infosniper_data.csv" );
  csvDownloadLink.style.visibility = 'hidden';
  document.body.appendChild( csvDownloadLink );
  csvDownloadLink.click();
  document.body.removeChild( csvDownloadLink );
}