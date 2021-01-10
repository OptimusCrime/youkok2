const handlebars = require('handlebars/runtime'),
  layouts = require('handlebars-layouts'),
  fs = require('fs');

const partial = fs.readFileSync('./templates/base.hbs', 'utf-8')
handlebars.registerPartial('base', partial);

handlebars.registerHelper(layouts(handlebars));

module.exports = handlebars;
