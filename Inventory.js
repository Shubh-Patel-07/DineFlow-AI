/ Inventory Manager JavaScript

let items = [];
let categories = [];

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeMockData();
    loadInventoryData();
    loadCategories();
    populateCategoryDropdown();
});

// Initialize mock data if empty
function initializeMockData() {
    if (!localStorage.getItem('inventoryCategories')) {
        const mockCategories = [
            { id: 1, name: 'Vegetables', description: 'Fresh vegetables', createdAt: new Date().toISOString() },
            { id: 2, name: 'Meats', description: 'Fresh and frozen meats', createdAt: new Date().toISOString() },
            { id: 3, name: 'Dairy', description: 'Dairy products', createdAt: new Date().toISOString() }
        ];
        localStorage.setItem('inventoryCategories', JSON.stringify(mockCategories));
    }

    if (!localStorage.getItem('inventoryItems')) {
        const mockItems = [
            { id: 1, name: 'Tomatoes', categoryId: 1, quantity: 50, minStock: 30, unit: 'kg', createdAt: new Date().toISOString() },
            { id: 2, name: 'Chicken Breast', categoryId: 2, quantity: 15, minStock: 20, unit: 'kg', createdAt: new Date().toISOString() },
            { id: 3, name: 'Milk', categoryId: 3, quantity: 100, minStock: 50, unit: 'liter', createdAt: new Date().toISOString() }
        ];
        localStorage.setItem('inventoryItems', JSON.stringify(mockItems));
    }
}

// Load inventory data
function loadInventoryData() {
    try {
        items = JSON.parse(localStorage.getItem('inventoryItems')) || [];
        renderItemsTable();
        renderLowStockTable();
    } catch (error) {
        console.error('Error loading inventory data:', error);
    }
}

// Load categories
function loadCategories() {
    try {
        categories = JSON.parse(localStorage.getItem('inventoryCategories')) || [];
        renderCategoriesTable();
    } catch (error) {
        console.error('Error loading categories:', error);
    }
}

// Populate category dropdown
function populateCategoryDropdown() {
    const select = document.getElementById('itemCategory');
    select.innerHTML = '<option value="">Select Category</option>';
    categories.forEach(cat => {
        const option = document.createElement('option');
        option.value = cat.id;
        option.textContent = cat.name;
        select.appendChild(option);
    });
}

// Render items table
function renderItemsTable() {
    const tableBody = document.getElementById('itemsTableBody');
    tableBody.innerHTML = '';

    if (items.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="7" style="text-align: center;">No items found</td></tr>';
        return;
    }

    items.forEach(item => {
        const status = getStockStatus(item.quantity, item.minStock);
        const statusClass = status === 'good' ? 'status-good' : status === 'low' ? 'status-low' : 'status-out';
        const row = `
            <tr>
                <td>${item.name}</td>
                <td>${getCategoryName(item.categoryId)}</td>
                <td><strong>${item.quantity}</strong></td>
                <td>${item.unit}</td>
                <td>${item.minStock}</td>
                <td><span class="stock-status ${statusClass}">${status.toUpperCase()}</span></td>
                <td>
                    <button class="btn-small btn-edit" onclick="editItem(${item.id})">Edit</button>
                    <button class="btn-small btn-delete" onclick="deleteItem(${item.id})">Delete</button>
                </td>
            </tr>
        `;
        tableBody.innerHTML += row;
    });
}

// Render categories table
function renderCategoriesTable() {
    const tableBody = document.getElementById('categoriesTableBody');
    tableBody.innerHTML = '';

    if (categories.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="4" style="text-align: center;">No categories found</td></tr>';
        return;
    }

    categories.forEach(cat => {
        const itemCount = items.filter(i => i.categoryId === cat.id).length;
        const row = `
            <tr>
                <td><strong>${cat.name}</strong></td>
                <td>${cat.description || '-'}</td>
                <td>${itemCount}</td>
                <td>
                    <button class="btn-small btn-edit" onclick="editCategory(${cat.id})">Edit</button>
                    <button class="btn-small btn-delete" onclick="deleteCategory(${cat.id})">Delete</button>
                </td>
            </tr>
        `;
        tableBody.innerHTML += row;
    });
}

// Render low stock table
function renderLowStockTable() {
    const tableBody = document.getElementById('lowstockTableBody');
    tableBody.innerHTML = '';

    const lowStockItems = items.filter(item => item.quantity <= item.minStock);

    if (lowStockItems.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 20px;">All items are well stocked!</td></tr>';
        return;
    }

    lowStockItems.forEach(item => {
        const shortage = item.minStock - item.quantity;
        const row = `
            <tr>
                <td><strong>${item.name}</strong></td>
                <td>${item.quantity}</td>
                <td>${item.minStock}</td>
                <td style="color: #e74c3c; font-weight: bold;">-${shortage} ${item.unit}</td>
                <td>
                    <button class="btn-small btn-low" onclick="openRestockModal(${item.id})">Restock</button>
                </td>
            </tr>
        `;
        tableBody.innerHTML += row;
    });
}

// Get stock status
function getStockStatus(quantity, minStock) {
    if (quantity <= 0) return 'out';
    if (quantity <= minStock) return 'low';
    return 'good';
}

// Get category name
function getCategoryName(categoryId) {
    const cat = categories.find(c => c.id === categoryId);
    return cat ? cat.name : 'N/A';
}

// Open add item modal
function openAddItemModal() {
    document.getElementById('addItemModal').classList.add('active');
}

// Close add item modal
function closeAddItemModal() {
    document.getElementById('addItemModal').classList.remove('active');
    document.getElementById('itemName').value = '';
    document.getElementById('itemCategory').value = '';
    document.getElementById('itemQuantity').value = '';
    document.getElementById('itemMinStock').value = '';
    document.getElementById('itemUnit').value = '';
}

// Handle add item
function handleAddItem(event) {
    event.preventDefault();

    const newItem = {
        id: items.length > 0 ? Math.max(...items.map(i => i.id)) + 1 : 1,
        name: document.getElementById('itemName').value,
        categoryId: parseInt(document.getElementById('itemCategory').value),
        quantity: parseInt(document.getElementById('itemQuantity').value),
        minStock: parseInt(document.getElementById('itemMinStock').value),
        unit: document.getElementById('itemUnit').value,
        createdAt: new Date().toISOString()
    };

    items.push(newItem);
    localStorage.setItem('inventoryItems', JSON.stringify(items));

    closeAddItemModal();
    loadInventoryData();
    populateCategoryDropdown();
    console.log('Item added successfully:', newItem);
}

// Edit item
function editItem(id) {
    const item = items.find(i => i.id === id);
    if (item) {
        alert(`Edit item: ${item.name}\nThis would open an edit dialog in a full implementation.`);
    }
}

// Delete item
function deleteItem(id) {
    if (confirm('Are you sure you want to delete this item?')) {
        items = items.filter(i => i.id !== id);
        localStorage.setItem('inventoryItems', JSON.stringify(items));
        loadInventoryData();
        console.log('Item deleted successfully');
    }
}

// Open add category modal
function openAddCategoryModal() {
    document.getElementById('addCategoryModal').classList.add('active');
}

// Close add category modal
function closeAddCategoryModal() {
    document.getElementById('addCategoryModal').classList.remove('active');
    document.getElementById('categoryName').value = '';
    document.getElementById('categoryDescription').value = '';
}

// Handle add category
function handleAddCategory(event) {
    event.preventDefault();

    const newCategory = {
        id: categories.length > 0 ? Math.max(...categories.map(c => c.id)) + 1 : 1,
        name: document.getElementById('categoryName').value,
        description: document.getElementById('categoryDescription').value,
        createdAt: new Date().toISOString()
    };

    categories.push(newCategory);
    localStorage.setItem('inventoryCategories', JSON.stringify(categories));

    closeAddCategoryModal();
    loadCategories();
    populateCategoryDropdown();
    console.log('Category added successfully:', newCategory);
}

// Edit category
function editCategory(id) {
    const cat = categories.find(c => c.id === id);
    if (cat) {
        alert(`Edit category: ${cat.name}\nThis would open an edit dialog in a full implementation.`);
    }
}

// Delete category
function deleteCategory(id) {
    if (confirm('Are you sure you want to delete this category?')) {
        categories = categories.filter(c => c.id !== id);
        localStorage.setItem('inventoryCategories', JSON.stringify(categories));
        loadCategories();
        populateCategoryDropdown();
        console.log('Category deleted successfully');
    }
}

// Open restock modal
function openRestockModal(itemId) {
    const item = items.find(i => i.id === itemId);
    if (item) {
        const quantity = prompt(`Restock ${item.name}\n\nEnter quantity to add (${item.unit}):`, '');
        if (quantity && !isNaN(quantity) && quantity > 0) {
            item.quantity += parseInt(quantity);
            localStorage.setItem('inventoryItems', JSON.stringify(items));
            loadInventoryData();
            console.log(`Restocked ${item.name} with ${quantity} ${item.unit}`);
        }
    }
}

// Search items
function searchItems() {
    const query = document.getElementById('searchInput').value.toLowerCase();
    const filteredItems = items.filter(item => 
        item.name.toLowerCase().includes(query) || 
        getCategoryName(item.categoryId).toLowerCase().includes(query)
    );

    const tableBody = document.getElementById('itemsTableBody');
    tableBody.innerHTML = '';

    if (filteredItems.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="7" style="text-align: center;">No items found</td></tr>';
        return;
    }

    filteredItems.forEach(item => {
        const status = getStockStatus(item.quantity, item.minStock);
        const statusClass = status === 'good' ? 'status-good' : status === 'low' ? 'status-low' : 'status-out';
        const row = `
            <tr>
                <td>${item.name}</td>
                <td>${getCategoryName(item.categoryId)}</td>
                <td><strong>${item.quantity}</strong></td>
                <td>${item.unit}</td>
                <td>${item.minStock}</td>
                <td><span class="stock-status ${statusClass}">${status.toUpperCase()}</span></td>
                <td>
                    <button class="btn-small btn-edit" onclick="editItem(${item.id})">Edit</button>
                    <button class="btn-small btn-delete" onclick="deleteItem(${item.id})">Delete</button>
                </td>
            </tr>
        `;
        tableBody.innerHTML += row;
    });
}

// Switch tabs
function switchTab(tabName) {
    const contents = document.querySelectorAll('.tab-content');
    const buttons = document.querySelectorAll('.tab-button');

    contents.forEach(content => content.classList.remove('active'));
    buttons.forEach(button => button.classList.remove('active'));

    document.getElementById(tabName).classList.add('active');
    event.target.classList.add('active');
             }
