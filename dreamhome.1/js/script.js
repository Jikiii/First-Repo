// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    const inputs = form.querySelectorAll('input[required], select[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('is-invalid');
            isValid = false;
        } else {
            input.classList.remove('is-invalid');
        }
    });
    
    return isValid;
}

// Auto-hide alerts after 3 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 3000);
    });
});

// Table search functionality
function searchTable(tableId, searchTerm) {
    const table = document.getElementById(tableId);
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        let found = false;
        const cells = row.getElementsByTagName('td');
        
        for (let j = 0; j < cells.length - 1; j++) {
            const cell = cells[j];
            if (cell.textContent.toLowerCase().includes(searchTerm.toLowerCase())) {
                found = true;
                break;
            }
        }
        
        row.style.display = found ? '' : 'none';
    }
}

// Export table to CSV
function exportToCSV(tableId, filename) {
    const table = document.getElementById(tableId);
    const rows = table.querySelectorAll('tr');
    const csv = [];
    
    rows.forEach(row => {
        const rowData = [];
        const cols = row.querySelectorAll('th, td');
        cols.forEach(col => {
            rowData.push('"' + col.innerText.replace(/"/g, '""') + '"');
        });
        csv.push(rowData.join(','));
    });
    
    const blob = new Blob([csv.join('\n')], { type: 'text/csv' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = filename + '.csv';
    link.click();
}

// Print page
function printPage() {
    window.print();
}

// Confirmation dialog
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

// Load data via AJAX
async function loadData(url, targetElementId) {
    try {
        const response = await fetch(url);
        const data = await response.json();
        const target = document.getElementById(targetElementId);
        
        if (target) {
            target.innerHTML = '';
            data.forEach(item => {
                const option = document.createElement('option');
                option.value = item.value;
                option.textContent = item.label;
                target.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error loading data:', error);
    }
}

// Live search
function liveSearch(inputId, tableId) {
    const input = document.getElementById(inputId);
    if (input) {
        input.addEventListener('keyup', function() {
            searchTable(tableId, this.value);
        });
    }
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    // Add search box to tables
    const tables = document.querySelectorAll('.table');
    tables.forEach(table => {
        const searchDiv = document.createElement('div');
        searchDiv.className = 'mb-3';
        searchDiv.innerHTML = `
            <input type="text" class="form-control" placeholder="Search in table..." 
                   onkeyup="searchTable('${table.id}', this.value)">
        `;
        table.parentNode.insertBefore(searchDiv, table);
    });
});

// Dynamic form fields
function addFormField(containerId, fieldHtml) {
    const container = document.getElementById(containerId);
    if (container) {
        const div = document.createElement('div');
        div.innerHTML = fieldHtml;
        container.appendChild(div.firstChild);
    }
}

// Remove form field
function removeFormField(element) {
    element.parentNode.removeChild(element);
}