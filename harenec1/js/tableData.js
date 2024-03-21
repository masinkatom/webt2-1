
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

    let modal = document.getElementById("modal2");
    $('#myTable tbody').on('click', 'tr', function () {
        let rowData = table.row(this).data();
        
        
        $.ajax({
            type: 'POST',
            url: './getRowData.php', // PHP script to fetch data from SQL database
            data: { 
                meno: rowData[1],
                priezvisko: rowData[2],
                organizacia: rowData[3]
            },
            success: function(response) {
                // Populate modal with fetched data
                $('.modal-data').html(response);

                // Display the modal
                modal.classList.remove("hidden");
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                alert('Error fetching data from server');
            }
        });

        // $('#input-name').val(rowData[0]);

        // $('#fetch').submit();

    });

    $('#close-modal').on('click', function () {
        modal.classList.add("hidden");
    });

    window.onclick = function(event) {
        if (event.target == modal) {
          modal.classList.add("hidden");
        }
    }

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