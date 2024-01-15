from flask import Flask, render_template, jsonify, request

import firebase_admin
from firebase_admin import credentials
from firebase_admin import db
import re

cred = credentials.Certificate('practica6-ffc02-662d19835b9f.json')
# firebase_admin.initialize_app(cred)

firebase_admin.initialize_app(cred, {
    'databaseURL' : 'https://practica6-ffc02-default-rtdb.firebaseio.com/'
})



app = Flask(__name__)

@app.route('/')
def index():
    return render_template('C:\xampp\htdocs\ServiciosWeb\PROYECTOFINALP2\BLOCKYBUSTERSPROJECT\signup.html')

@app.route('/ping')
def ping():
    return jsonify({'message':"pong!"})

def is_valid_mail(mail):
    regular_expression = r"(?:[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*|\"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*\")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:(2(5[0-5]|[0-4][0-9])|1[0-9][0-9]|[1-9]?[0-9]))\.){3}(?:(2(5[0-5]|[0-4][0-9])|1[0-9][0-9]|[1-9]?[0-9])|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])"
    return re.match(regular_expression, mail) is not None

def isThereNumber(str1):
   number_flag = False
   for i in str1:
        if i.isdigit():
            number_flag = True
            return number_flag
      
@app.route('/signup', methods=['POST'])
def addAccount():
    new_account = {
        "code": '999',
        "name": request.json['name'], #James
        "mail":  request.json['mail'], #'James123@gmail.com',
        "password": request.json['password'] #'Password123'
    }
   
    data = db.reference('usuarios_sistema/'+new_account['name'])
    
    # Read the data at the posts reference (this is a blocking operation)
    print(data.get())
    
    if(str(data.get()).lower() == 'none'):
        # print('no existe')
        if(  is_valid_mail(request.json['mail']) == True): #validacion de correo '@' in request.json['mail'] == True and
            if(len(request.json['password']) > 5 and isThereNumber(request.json['password']) == True #  request.json['password'].isdigit() == True 
                and re.search('[A-Z]', request.json['password'] ) != None):
                table = db.reference('usuarios_sistema/')
                res = table.child(new_account['name'])
                new_account['code'] = '201'
                res.update(new_account)
                # print(new_account)
            else:
                new_account['code'] = '603 Contrasena no segura'
        else:
            new_account['code'] = '602 Correo mal formado'
    else:
        # print('ya existe')
        new_account['code'] = '601 Usuario ya existe'

    return new_account

if __name__ == '__main__':
    app.run(debug=True, port=4000)