/**
 * Registers Dynamic Elements as Gutenberg Blocks
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

// Register Gutenberg Blocks if the Gutenberg Editor is loaded on screen.
// This prevents JS errors if this script is accidentally enqueued on a non-
// Gutenberg editor screen, or the Classic Editor Plugin is active.
if ( typeof wp !== 'undefined' &&
	typeof wp.data !== 'undefined' &&
	typeof wp.data.dispatch( 'core/edit-post' ) !== 'undefined' &&
	wp.data.dispatch( 'core/edit-post' ) !== null ) {

	if ( typeof page_generator_pro_gutenberg != 'undefined' ) {

		// Remove the Permalink Panel, if we're using Gutenberg on Content Groups.
		if ( page_generator_pro_gutenberg.post_type == 'page-generator-pro' ) {
			wp.data.dispatch( 'core/edit-post' ).removeEditorPanel( 'post-link' );
		}

		// Register each Block in Gutenberg.
		for ( const block in page_generator_pro_gutenberg.shortcodes ) {
			pageGeneratorProGutenbergRegisterBlock( page_generator_pro_gutenberg.shortcodes[ block ] );
		}

		// Initialize conditional fields.
		page_generator_pro_conditional_fields_initialize();

	}

}

/**
 * Registers the given block in Gutenberg
 *
 * @since 	2.5.4
 *
 * @param 	object 	block 	Block
 */
function pageGeneratorProGutenbergRegisterBlock( block ) {

	// Register Block.
	( function( blocks, editor, element, components, block ) {

		// Define some constants for the various items we'll use.
		const el                              = element.createElement;
		const { registerBlockType }           = blocks;
		const { RichText, InspectorControls } = editor;
		const { Fragment }                    = element;
		const {
			TextControl,
			CheckboxControl,
			RadioControl,
			SelectControl,
			TextareaControl,
			ToggleControl,
			RangeControl,
			FormTokenField,
			Panel,
			PanelBody,
			PanelRow,
			ServerSideRender
		}                                     = components;

		// Build Icon, if it's an object.
		var icon = 'dashicons-tablet';
		if ( typeof block.gutenberg_icon !== 'undefined' ) {
			if ( block.gutenberg_icon.search( 'svg' ) >= 0 ) {
				// SVG.
				icon = element.RawHTML(
					{
						children: block.gutenberg_icon
					}
				);
			} else {
				// Dashicon.
				icon = block.gutenberg_icon;
			}
		}

		// Register Block.
		registerBlockType(
			'page-generator-pro/' + block.name,
			{
				title:      block.title,
				description:block.description,
				category:   block.category,
				icon:       icon,
				keywords: 	block.keywords,
				attributes: block.attributes,

				// Required to force a preview.
				example: 	{
					attributes: {
						is_gutenberg_example: true, // This can be anything.
					}
				},

				// Editor.
				edit: function( props ) {

					// Build Inspector Control Panels, which will appear in the Sidebar when editing the Block.
					var panels  = [],
					initialOpen = true;
					for ( const panel in block.tabs ) {

						// Build Inspector Control Panel Rows, one for each Field.
						var rows = [];
						for ( var i in block.tabs[ panel ].fields ) {
							const attribute = block.tabs[ panel ].fields[ i ], // e.g. 'term'.
							field           = block.fields[ attribute ]; // field array.

							var fieldElement,
							fieldClassNames = [], // css class(es) array to apply to the block field.
							fieldProperties = {}; // holds the block field's properties.

							// Build CSS class name(s).
							if ( typeof field.class !== 'undefined' ) {
								fieldClassNames.push( field.class );
							}
							if ( typeof field.condition !== 'undefined' ) {
								fieldClassNames.push( field.condition.value );
							}

							// Define Field's Properties.
							var fieldProperties = {
								id:  		'page_generator_pro_' + block.name + '_' + attribute,
								label: 		field.label,
								help: 		field.description,
								className: 	fieldClassNames.join( ' ' ),
								value: 		props.attributes[ attribute ],
								onChange: 	function( value ) {
									if ( field.type == 'number' ) {
										// If value is a blank string i.e. no attribute value was provided,
										// cast it to the field's minimum number setting.
										// This prevents WordPress' block renderer API returning a 400 error
										// because a blank value will be passed as a string, when WordPress
										// expects it to be a numerical value.
										if ( value === '' ) {
											value = field.min;
										}

										// Cast value to integer if a value exists.
										if ( value.length > 0 ) {
											value = Number( value );
										}
									}

									var newValue          = {};
									newValue[ attribute ] = value;
									props.setAttributes( newValue );
								}
							};

							// Add data- attributes.
							if ( typeof field.data !== 'undefined' ) {
								for ( var key in field.data ) {
									fieldProperties[ 'data-' + key ] = field.data[ key ];
								}
							}

							// Define additional Field Properties and the Field Element,
							// depending on the Field Type (select, textarea, text etc).
							switch ( field.type ) {

								case 'select':
									// Build options for <select> input.
									var fieldOptions = [];
									for ( var value in field.values ) {
										fieldOptions.push(
											{
												label: field.values[ value ],
												value: value
											}
										);
									}

									// Sort field's options alphabetically by label.
									fieldOptions.sort(
										function ( x, y ) {

											let a = x.label.toUpperCase(),
											b     = y.label.toUpperCase();
											return a.localeCompare( b );

										}
									);

									// Define field properties.
									fieldProperties.options = fieldOptions;

									// Define field element.
									fieldElement = el(
										SelectControl,
										fieldProperties
									);
									break;

								case 'select_multiple':
									// Build values for <select> inputs as a flat array comprising of the format:
									// [key] label.
									// onChange will extract the key from [key] and store it as the value.
									var fieldSuggestions = [];
									for ( var value in field.values ) {
										fieldSuggestions.push( '[' + value + '] ' + field.values[ value ] );
									}

									// Define field properties.
									fieldProperties.suggestions    = fieldSuggestions;
									fieldProperties.maxSuggestions = 5;
									fieldProperties.onChange       = function( values ) {

										// Extract keys between square brackets, storing as the value.
										var newValues = [],
										length        = values.length;
										for ( index = 0; index < length; index++ ) {
											var matches = values[ index ].match( /\[(.*?)\]/ );
											if ( matches ) {
												newValues.push( matches[1] );
											} else {
												newValues.push( values[ index ] );
											}
										}

										// Assign to block.
										var newValue          = {};
										newValue[ attribute ] = newValues;
										props.setAttributes( newValue );

									}

									// Define field element.
									fieldElement = el(
										FormTokenField,
										fieldProperties
									);
									break;

								case 'text_multiple':
									// Define field element.
									fieldElement = el(
										FormTokenField,
										fieldProperties
									);
									break;

								case 'toggle':
									// Define field properties.
									fieldProperties.checked = props.attributes[ attribute ];

									// Define field element.
									fieldElement = el(
										ToggleControl,
										fieldProperties
									);
									break;

								case 'number':
									// Define field properties.
									fieldProperties.type = field.type;
									fieldProperties.min  = field.min;
									fieldProperties.max  = field.max;
									fieldProperties.step = field.step;

									// Define field element.
									fieldElement = el(
										TextControl,
										fieldProperties
									);
									break;

								case 'autocomplete':
									// Define field properties.
									fieldProperties.list    = 'autocomplete-' + i;
									fieldProperties.type    = 'text';
									fieldProperties.options = field.values;

									// Define field element.
									fieldElement = el(
										WPZincAutocompleterControl,
										fieldProperties
									);
									break;

								default:
									// Define field properties.
									fieldProperties.type = field.type;

									// Define field element.
									fieldElement = el(
										TextControl,
										fieldProperties
									);
									break;
							}

							// Add Field as a Row.
							rows.push(
								el(
									PanelRow,
									{
										key: attribute // Prevents "Warning: Each child in a list should have a unique "key" prop.".
									},
									fieldElement
								)
							);
						}

						// Add the Panel Rows to a new Panel.
						panels.push(
							el(
								PanelBody,
								{
									title: block.tabs[ panel ].label,
									key: panel,  // Prevents "Warning: Each child in a list should have a unique "key" prop.".
									initialOpen: initialOpen
								},
								rows
							)
						);

						// Don't open any further panels.
						initialOpen = false;
					}

					// Generate Block Preview.
					var preview = '';
					if ( block.register_on_generation_only ) {
						// Output a preview that describes the block, and to click on it to open the settings sidebar.
						// The block will be converted into HTML upon generation, so there's no 'true' preview to display.
						preview = el(
							'div',
							{
								className: 'page-generator-pro-block ' + block.name
							},
							el(
								'div',
								{
									className: 'page-generator-pro-block-title'
								},
								block.title + ' Dynamic Element'
							),
							el(
								'div',
								{
									className: 'page-generator-pro-block-description'
								},
								block.description
							),
							el(
								'div',
								{
									className: 'page-generator-pro-block-description'
								},
								'Click this block to open the settings sidebar.'
							)
						);
					} else {
						// Use the block's PHP's render() function by calling the ServerSideRender component, as this will
						// also be called when viewing a generated page.
						preview = el(
							ServerSideRender,
							{
								block: 'page-generator-pro/' + block.name,
								attributes: props.attributes,
								className: 'page-generator-pro-' + block.name,
							}
						);
					}

					// If this block has been selected, open the editor sidebar now.
					if ( props.isSelected ) {
						if ( ! wp.data.select( 'core/edit-post' ).isEditorSidebarOpened() ) {
							wp.data.dispatch( 'core/edit-post' ).openGeneralSidebar( 'edit-post/block' );
						}
					}

					// Return.
					return (
						el(
							Fragment,
							{},
							el(
								InspectorControls,
								{
									key: block.name
								},
								panels
							),
							// Block Preview.
							preview
						)
					);
				},

				// Output.
				save: function( props ) {

					return null;

				}
			}
		);

	} (
		window.wp.blocks,
		window.wp.blockEditor,
		window.wp.element,
		window.wp.components,
		block
	) );

}
