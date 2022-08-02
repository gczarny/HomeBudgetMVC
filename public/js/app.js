/**
         * Add jQuery Validation plugin method for a valid password
         *
         * Valid passwords contain at least one letter and one number.
         */
 $.validator.addMethod('validPassword',
 function(value, element, param) {
     if (value != '') {
         if (value.match(/.*[a-z]+.*/i) == null) {
             return false;
         }
         if (value.match(/.*\d+.*/) == null) {
             return false;
         }
     }

     return true;
 },
 'Must contain at least one letter and one number'
);


function getCategoriesSum(table) {
  var categoryCounts = {};
  var amount = {};

  // Get the row indexes for the rows displayed under the current search
  var indexes = table.rows({ search: 'applied', page: 'current'}).indexes().toArray();

  // For each row, extract the name Of Category and add the amount to the array
  for (var i = 0; i < indexes.length; i++) {
      var nameOfCategory = table.cell(indexes[i], 2).data();
      if (categoryCounts[nameOfCategory] === undefined) {
        categoryCounts[nameOfCategory] = [
              +table
                  .cell(indexes[i], 1)
                  .data()
                  //.replace(/[^0-9.]/g, ''),
          ];
      } else {
        categoryCounts[nameOfCategory].push(
              +table
                  .cell(indexes[i], 1)
                  .data()
                  //.replace(/[^0-9.]/g, '')
          );
      }
  }

  // Extract the names Of Category that are present in the table
  var keys = Object.keys(categoryCounts);

  // For each name Of Category work out the average salamount
  for (var i = 0; i < keys.length; i++) {
      var length = categoryCounts[keys[i]].length;
      var total = categoryCounts[keys[i]].reduce((a, b) => a + b, 0);
      amount[keys[i]] = total /// length;
  }
  return amount;
}

