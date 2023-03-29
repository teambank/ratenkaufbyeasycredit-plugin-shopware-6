import { build } from 'esbuild'
import browserslistToEsbuild from 'browserslist-to-esbuild'

const config = {
  entryPoints: ['src/Resources/app/storefront/src/_main.js'],
  outfile: 'src/Resources/public/static/js/easycredit.js',
  bundle: true,
  target: browserslistToEsbuild(),
  loader: {
    '.class': 'ts'
  }
}

build(config).catch(() => process.exit(1))
build({
  ...config, 
  outfile: 'src/Resources/public/static/js/easycredit.min.js',
  minify: true
}).catch(() => process.exit(1))
