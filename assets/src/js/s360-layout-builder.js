// Require the stlye files inside the "src/scss" folder.
require('SRC_STYLES/s360-layout-builder.scss');

// Require all static images inside the "src/images" folder.
require.context('SRC_IMAGES', true, /\.(gif|png|jpe?g|svg)$/);
