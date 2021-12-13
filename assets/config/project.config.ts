import * as path from 'path';

const PROJECT_CONFIG = {
  projectName: 's360-layout-builder',
  pkg: require(path.resolve('package.json')),
  entryPoints: ['s360-layout-builder.js','s360-layout-builder.fontawesome.js']
};

export { PROJECT_CONFIG };
