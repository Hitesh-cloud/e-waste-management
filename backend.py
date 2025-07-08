from flask import Flask, request, jsonify
from flask_sqlalchemy import SQLAlchemy
from flask_cors import CORS
from sqlalchemy.exc import IntegrityError
import os

app = Flask(__name__)
CORS(app)

app.config['SQLALCHEMY_DATABASE_URI'] = os.getenv('DATABASE_URL', 'mysql://hitesh:hitesh123@localhost/ecorecycle')
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False
db = SQLAlchemy(app)

class User(db.Model):
    __tablename__ = 'users'
    
    id = db.Column(db.Integer, primary_key=True)
    username = db.Column(db.String(50), unique=True, nullable=False)
    email = db.Column(db.String(100), unique=True, nullable=False)
    items = db.relationship('ElectronicItem', backref='seller', lazy=True)

    def to_dict(self):
        return {
            'id': self.id,
            'username': self.username,
            'email': self.email
        }

class ElectronicItem(db.Model):
    __tablename__ = 'electronic_items'
    
    id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(100), nullable=False)
    category = db.Column(db.String(50), nullable=False)
    condition = db.Column(db.String(50), nullable=False)
    seller_id = db.Column(db.Integer, db.ForeignKey('users.id'), nullable=False)

    def to_dict(self):
        return {
            'id': self.id,
            'name': self.name,
            'category': self.category,
            'condition': self.condition,
            'seller_id': self.seller_id,
            'seller_name': self.seller.username if self.seller else None
        }

@app.before_first_request
def create_tables():
    db.create_all()

@app.route('/')
def index():
    return jsonify({'message': 'Welcome to the EcoRecycle API', 'status': 'running'}), 200

@app.route('/api/users', methods=['POST'])
def add_user():
    try:
        data = request.get_json()
        
        # Validate input
        if not data or not data.get('username') or not data.get('email'):
            return jsonify({'error': 'Username and email are required'}), 400

        new_user = User(username=data['username'], email=data['email'])
        db.session.add(new_user)
        db.session.commit()
        
        return jsonify({
            'message': 'User created successfully', 
            'user': new_user.to_dict()
        }), 201

    except IntegrityError:
        db.session.rollback()
        return jsonify({'error': 'Username or email already exists'}), 409
    except Exception as e:
        db.session.rollback()
        return jsonify({'error': f'Server error: {str(e)}'}), 500

@app.route('/api/users', methods=['GET'])
def get_users():
    try:
        users = User.query.all()
        return jsonify([user.to_dict() for user in users]), 200
    except Exception as e:
        return jsonify({'error': f'Server error: {str(e)}'}), 500

@app.route('/api/users/<int:user_id>', methods=['GET'])
def get_user(user_id):
    try:
        user = User.query.get(user_id)
        if not user:
            return jsonify({'error': 'User not found'}), 404
        return jsonify(user.to_dict()), 200
    except Exception as e:
        return jsonify({'error': f'Server error: {str(e)}'}), 500

@app.route('/api/items', methods=['POST'])
def add_item():
    try:
        data = request.get_json()
        required_fields = ['name', 'category', 'condition', 'seller_id']
        if not all(field in data for field in required_fields):
            return jsonify({'error': 'Missing required fields: name, category, condition, seller_id'}), 400
        seller = User.query.get(data['seller_id'])
        if not seller:
            return jsonify({'error': 'Seller not found'}), 404
        new_item = ElectronicItem(
            name=data['name'],
            category=data['category'],
            condition=data['condition'],
            seller_id=data['seller_id']
        )
        db.session.add(new_item)
        db.session.commit()

        return jsonify({
            'message': 'Item added successfully', 
            'item': new_item.to_dict()
        }), 201

    except Exception as e:
        db.session.rollback()
        return jsonify({'error': f'Server error: {str(e)}'}), 500

@app.route('/api/items', methods=['GET'])
def get_items():
    try:
        items = ElectronicItem.query.all()
        return jsonify([item.to_dict() for item in items]), 200
    except Exception as e:
        return jsonify({'error': f'Server error: {str(e)}'}), 500

@app.route('/api/items/<int:item_id>', methods=['GET'])
def get_item(item_id):
    try:
        item = ElectronicItem.query.get(item_id)
        if not item:
            return jsonify({'error': 'Item not found'}), 404
        return jsonify(item.to_dict()), 200
    except Exception as e:
        return jsonify({'error': f'Server error: {str(e)}'}), 500

@app.route('/api/items/<int:item_id>', methods=['PUT'])
def update_item(item_id):
    try:
        item = ElectronicItem.query.get(item_id)
        if not item:
            return jsonify({'error': 'Item not found'}), 404

        data = request.get_json()
        
        # Update fields if provided
        if 'name' in data:
            item.name = data['name']
        if 'category' in data:
            item.category = data['category']
        if 'condition' in data:
            item.condition = data['condition']

        db.session.commit()
        return jsonify({
            'message': 'Item updated successfully', 
            'item': item.to_dict()
        }), 200

    except Exception as e:
        db.session.rollback()
        return jsonify({'error': f'Server error: {str(e)}'}), 500

@app.route('/api/items/<int:item_id>', methods=['DELETE'])
def delete_item(item_id):
    try:
        item = ElectronicItem.query.get(item_id)
        if not item:
            return jsonify({'error': 'Item not found'}), 404

        db.session.delete(item)
        db.session.commit()
        return jsonify({'message': 'Item deleted successfully'}), 200

    except Exception as e:
        db.session.rollback()
        return jsonify({'error': f'Server error: {str(e)}'}), 500

@app.route('/api/health', methods=['GET'])
def health_check():
    try:
        # Test database connection
        db.session.execute('SELECT 1')
        return jsonify({'status': 'healthy', 'database': 'connected'}), 200
    except Exception as e:
        return jsonify({'status': 'unhealthy', 'error': str(e)}), 500

@app.errorhandler(404)
def not_found(error):
    return jsonify({'error': 'Route not found'}), 404

@app.errorhandler(500)
def internal_error(error):
    return jsonify({'error': 'Internal server error'}), 500

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5000)
