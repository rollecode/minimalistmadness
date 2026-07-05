/**
 * Block editor extras: custom block styles. Buildless, uses editor globals.
 */

wp.blocks.registerBlockStyle('core/paragraph', {
  name: 'boxed',
  label: 'Laatikko',
});

wp.blocks.registerBlockStyle('core/list', {
  name: 'no-bullets',
  label: 'Ilman listamerkkejä',
});

wp.blocks.registerBlockStyle('core/list', {
  name: 'todo-list',
  label: 'Todo-lista',
});

wp.blocks.registerBlockStyle('core/list-item', {
  name: 'checked',
  label: 'Tehty tehtävä',
});

wp.blocks.registerBlockStyle('core/list', {
  name: 'green-shades',
  label: 'Vihreät tasot',
});

wp.blocks.registerBlockStyle('core/list', {
  name: 'red-shades',
  label: 'Punaiset tasot',
});
