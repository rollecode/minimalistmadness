/**
 * Diary metadata sidebar panel for the block editor.
 *
 * Hand-written with wp.element/wp.data (no JSX, no build step) so the theme
 * stays buildless. Reads and writes the same post meta keys that ACF used,
 * registered for REST in inc/hooks/diary-meta.php. Shown only on diary posts.
 */
( function ( wp ) {
  if ( ! wp || ! wp.plugins || ! wp.element ) {
    return;
  }

  var el = wp.element.createElement;
  var registerPlugin = wp.plugins.registerPlugin;
  var PluginDocumentSettingPanel =
    ( wp.editor && wp.editor.PluginDocumentSettingPanel ) ||
    ( wp.editPost && wp.editPost.PluginDocumentSettingPanel );
  var components = wp.components;
  var useSelect = wp.data.useSelect;
  var useEntityProp = wp.coreData.useEntityProp;
  var __ = wp.i18n ? wp.i18n.__ : function ( s ) { return s; };
  var data = window.rollemaaDiaryMeta || { choices: {} };

  if ( ! PluginDocumentSettingPanel ) {
    return;
  }

  // Build SelectControl options from a { value: label } choice map.
  function selectOptions( map ) {
    var opts = [ { label: '—', value: '' } ];
    Object.keys( map || {} ).forEach( function ( value ) {
      opts.push( { label: map[ value ], value: value } );
    } );
    return opts;
  }

  function Panel() {
    var postType = useSelect( function ( select ) {
      return select( 'core/editor' ).getCurrentPostType();
    }, [] );

    if ( postType !== 'diary' ) {
      return null;
    }

    var entity = useEntityProp( 'postType', 'diary', 'meta' );
    var meta = entity[ 0 ];
    var setMeta = entity[ 1 ];

    // Returns an onChange handler that merges a single key into meta.
    function set( key ) {
      return function ( value ) {
        var next = {};
        next[ key ] = value;
        setMeta( Object.assign( {}, meta, next ) );
      };
    }

    function text( key, label ) {
      return el( components.TextControl, {
        label: label,
        value: meta[ key ] || '',
        onChange: set( key ),
        __nextHasNoMarginBottom: true,
      } );
    }

    function range( key, label ) {
      return el( components.RangeControl, {
        label: label,
        value: meta[ key ] ? parseInt( meta[ key ], 10 ) : 0,
        min: 0,
        max: 100,
        // Store as a string to match the existing ACF-written values.
        onChange: function ( value ) {
          set( key )( value === undefined || value === null ? '' : String( value ) );
        },
        __nextHasNoMarginBottom: true,
      } );
    }

    function select( key, label ) {
      return el( components.SelectControl, {
        label: label,
        value: meta[ key ] || '',
        options: selectOptions( data.choices[ key ] ),
        onChange: set( key ),
        __nextHasNoMarginBottom: true,
      } );
    }

    return el(
      PluginDocumentSettingPanel,
      { name: 'rollemaa-diary-meta', title: __( 'Päivän metatiedot', 'minimalistmadness' ) },
      text( 'gratitude', 'Kiitollisuus' ),
      select( 'mood', 'Mieliala kirjoittamishetkellä' ),
      range( 'mood_scale', 'Mieliala %' ),
      range( 'energy_scale', 'Energiataso %' ),
      range( 'anxiety_scale', 'Ahdistus %' ),
      range( 'productivity_scale', 'Tuottavuus %' ),
      range( 'habits_percent', 'Päivätavoitteet %' ),
      text( 'highlight', 'Päivän kohokohta' ),
      text( 'np', 'Nyt soi' ),
      text( 'np_link', 'Nyt soivan biisin linkki' ),
      select( 'device', 'Laite' ),
      select( 'weather_icon', 'Sään kuvake' ),
      text( 'weather_text', 'Sää tekstinä' ),
      text( 'temperature', 'Lämpötila' ),
      text( 'location', 'Sijainti' ),
      select( 'drink_icon', 'Juoman kuvake' ),
      text( 'drink_text', 'Juoman teksti' )
    );
  }

  registerPlugin( 'rollemaa-diary-meta', { render: Panel, icon: null } );
} )( window.wp );
