
$(document).ready(function () {
    document.getElementById("page-length").addEventListener("input", handleTableRows);
    document.getElementById("filter-year").addEventListener("input", filterYearRows);
    document.getElementById("filter-category").addEventListener("input", filterCategoryRows);

    var table;
    fetch('./getTableData.php')
        .then(response => response.json()) // Parse the response as text
        .then(data => {
            console.log("Data received from server:", data); // Log the parsed JSON data

            // Initialize DataTables with the fetched data
            table = $('#myTable').DataTable({
                responsive: true,
                data: data,
                scrollX: true,
                layout: {
                    topStart: null,
                    topEnd: null,
                    bottomStart: null,
                    bottomEnd: 'paging'
                }
            });

            
        }) // Closing parenthesis for the then method
        .catch(error => {
            console.error('Error fetching data:', error);
        }
    );


    $('#myTable tbody').on('click', 'tr', function () {
        var rowData = table.row(this).data(); // Get data of clicked row
        console.log(rowData); // Log row data to console

    });

    function handleTableRows(e) {
        table.page.len(e.target.value).draw();
    }

    function filterYearRows(e) {
        table.column(0).search(e.target.value).draw();
    }

    function filterCategoryRows(e) {
        table.column(5).search(e.target.value).draw();
    }


});