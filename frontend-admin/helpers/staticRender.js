const fs = require('fs');

module.exports = function(arg, options) {
  fs.writeFile('./test1.json', JSON.stringify(arg), err => console.error(err));
  debugger;
  //fs.writeFile('./test2.json', JSON.stringify(options), err => console.error(err));
  return 'I was called';
};
