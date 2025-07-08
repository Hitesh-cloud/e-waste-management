<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoRecycle - Electronic Items Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-section {
            background-color: #ecf0f1;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
        }
        button:hover {
            background-color: #2980b9;
        }
        .success {
            color: #27ae60;
            font-weight: bold;
        }
        .error {
            color: #e74c3c;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #3498db;
            color: white;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .loading {
            text-align: center;
            padding: 20px;
            color: #666;
        }
        .tabs {
            display: flex;
            margin-bottom: 20px;
        }
        .tab {
            padding: 10px 20px;
            background-color: #ecf0f1;
            cursor: pointer;
            border: none;
            margin-right: 5px;
        }
        .tab.active {
            background-color: #3498db;
            color: white;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🌱 EcoRecycle - Electronic Items Management</h1>
        
        <div class="tabs">
            <button class="tab active" onclick="showTab('items')">Items</button>
            <button class="tab" onclick="showTab('users')">Users</button>
            <button class="tab" onclick="showTab('add-item')">Add Item</button>
            <button class="tab" onclick="showTab('add-user')">Add User</button>
        </div>

        <div id="items" class="tab-content active">
            <h2>Electronic Items</h2>
            <button onclick="loadItems()">Refresh Items</button>
            <div id="items-loading" class="loading" style="display: none;">Loading items...</div>
            <div id="items-error" class="error" style="display: none;"></div>
            <div id="items-table"></div>
        </div>

        <div id="users" class="tab-content">
            <h2>Users</h2>
            <button onclick="loadUsers()">Refresh Users</button>
            <div id="users-loading" class="loading" style="display: none;">Loading users...</div>
            <div id="users-error" class="error" style="display: none;"></div>
            <div id="users-table"></div>
        </div>

        <div id="add-item" class="tab-content">
            <div class="form-section">
                <h2>Add New Item</h2>
                <form id="add-item-form">
                    <div class="form-group">
                        <label for="item-name">Item Name:</label>
                        <input type="text" id="item-name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="item-category">Category:</label>
                        <select id="item-category" name="category" required>
                            <option value="">Select Category</option>
                            <option value="Smartphone">Smartphone</option>
                            <option value="Laptop">Laptop</option>
                            <option value="Tablet">Tablet</option>
                            <option value="Desktop">Desktop</option>
                            <option value="Monitor">Monitor</option>
                            <option value="Printer">Printer</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="item-condition">Condition:</label>
                        <select id="item-condition" name="condition" required>
                            <option value="">Select Condition</option>
                            <option value="New">New</option>
                            <option value="Like New">Like New</option>
                            <option value="Good">Good</option>
                            <option value="Fair">Fair</option>
                            <option value="Poor">Poor</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="item-seller">Seller:</label>
                        <select id="item-seller" name="seller_id" required>
                            <option value="">Select Seller</option>
                        </select>
                    </div>
                    <button type="submit">Add Item</button>
                </form>
                <div id="add-item-message"></div>
            </div>
        </div>

        <div id="add-user" class="tab-content">
            <div class="form-section">
                <h2>Add New User</h2>
                <form id="add-user-form">
                    <div class="form-group">
                        <label for="user-username">Username:</label>
                        <input type="text" id="user-username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="user-email">Email:</label>
                        <input type="email" id="user-email" name="email" required>
                    </div>
                    <button type="submit">Add User</button>
                </form>
                <div id="add-user-message"></div>
            </div>
        </div>
    </div>

    <script>
        const API_BASE_URL = 'http://localhost:5000/api';

        function showTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
            
            if (tabName === 'items') {
                loadItems();
            } else if (tabName === 'users') {
                loadUsers();
            } else if (tabName === 'add-item') {
                loadSellersDropdown();
            }
        }

        function showMessage(elementId, message, isError = false) {
            const element = document.getElementById(elementId);
            element.textContent = message;
            element.className = isError ? 'error' : 'success';
            element.style.display = 'block';
            
            setTimeout(() => {
                element.style.display = 'none';
            }, 5000);
        }

        function showLoading(elementId, show = true) {
            document.getElementById(elementId).style.display = show ? 'block' : 'none';
        }

        async function apiRequest(endpoint, options = {}) {
            try {
                const response = await fetch(`${API_BASE_URL}${endpoint}`, {
                    headers: {
                        'Content-Type': 'application/json',
                        ...options.headers
                    },
                    ...options
                });

                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.error || 'API request failed');
                }
                
                return data;
            } catch (error) {
                console.error('API request failed:', error);
                throw error;
            }
        }

        async function loadItems() {
            showLoading('items-loading', true);
            document.getElementById('items-error').style.display = 'none';
            
            try {
                const items = await apiRequest('/items');
                displayItems(items);
            } catch (error) {
                document.getElementById('items-error').textContent = `Error loading items: ${error.message}`;
                document.getElementById('items-error').style.display = 'block';
            }
            
            showLoading('items-loading', false);
        }

        function displayItems(items) {
            const tableContainer = document.getElementById('items-table');
            
            if (items.length === 0) {
                tableContainer.innerHTML = '<p>No items found.</p>';
                return;
            }

            let html = `
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Condition</th>
                        <th>Seller</th>
                        <th>Actions</th>
                    </tr>
            `;

            items.forEach(item => {
                html += `
                    <tr>
                        <td>${item.id}</td>
                        <td>${item.name}</td>
                        <td>${item.category}</td>
                        <td>${item.condition}</td>
                        <td>${item.seller_name || 'Unknown'}</td>
                        <td>
                            <button onclick="deleteItem(${item.id})" style="background-color: #e74c3c;">Delete</button>
                        </td>
                    </tr>
                `;
            });

            html += '</table>';
            tableContainer.innerHTML = html;
        }

        async function loadUsers() {
            showLoading('users-loading', true);
            document.getElementById('users-error').style.display = 'none';
            
            try {
                const users = await apiRequest('/users');
                displayUsers(users);
            } catch (error) {
                document.getElementById('users-error').textContent = `Error loading users: ${error.message}`;
                document.getElementById('users-error').style.display = 'block';
            }
            
            showLoading('users-loading', false);
        }

        function displayUsers(users) {
            const tableContainer = document.getElementById('users-table');
            
            if (users.length === 0) {
                tableContainer.innerHTML = '<p>No users found.</p>';
                return;
            }

            let html = `
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                    </tr>
            `;

            users.forEach(user => {
                html += `
                    <tr>
                        <td>${user.id}</td>
                        <td>${user.username}</td>
                        <td>${user.email}</td>
                    </tr>
                `;
            });

            html += '</table>';
            tableContainer.innerHTML = html;
        }

        async function loadSellersDropdown() {
            try {
                const users = await apiRequest('/users');
                const dropdown = document.getElementById('item-seller');
                
                dropdown.innerHTML = '<option value="">Select Seller</option>';
                
                users.forEach(user => {
                    const option = document.createElement('option');
                    option.value = user.id;
                    option.textContent = user.username;
                    dropdown.appendChild(option);
                });
            } catch (error) {
                console.error('Error loading sellers:', error);
            }
        }

        async function deleteItem(itemId) {
            if (!confirm('Are you sure you want to delete this item?')) {
                return;
            }

            try {
                await apiRequest(`/items/${itemId}`, { method: 'DELETE' });
                loadItems();
                alert('Item deleted successfully!');
            } catch (error) {
                alert(`Error deleting item: ${error.message}`);
            }
        }

        document.getElementById('add-item-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData);
            
            try {
                await apiRequest('/items', {
                    method: 'POST',
                    body: JSON.stringify(data)
                });
                
                showMessage('add-item-message', 'Item added successfully!');
                e.target.reset();
            } catch (error) {
                showMessage('add-item-message', `Error adding item: ${error.message}`, true);
            }
        });

        document.getElementById('add-user-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData);
            
            try {
                await apiRequest('/users', {
                    method: 'POST',
                    body: JSON.stringify(data)
                });
                
                showMessage('add-user-message', 'User added successfully!');
                e.target.reset();
            } catch (error) {
                showMessage('add-user-message', `Error adding user: ${error.message}`, true);
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            loadItems();
        });
    </script>
</body>
</html>
