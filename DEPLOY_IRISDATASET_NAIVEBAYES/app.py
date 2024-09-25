import pandas as pd
import numpy as np
from flask import Flask, render_template, request
import pickle

app = Flask(__name__)
model = pickle.load(open('model_naive_bayes.pkl', 'rb'))

# Pemetaan label numerik ke nama kelas bunga iris
label_mapping = {0: 'Iris-setosa', 1: 'Iris-versicolor', 2: 'Iris-virginica'}

@app.route('/')
def home():
    return render_template('indexprogram.html')

@app.route('/Predict', methods=["POST"])
def Predict():
    float_features = [float(x) for x in request.form.values()]
    feature = np.array([float_features])
    prediction = model.predict(feature)
    
    # Menggunakan label mapping untuk mengonversi hasil numerik ke nama kelas
    prediction_text = "[ " + ", ".join(label_mapping.get(p, 'Unknown') for p in prediction) + " ]"
    
    return render_template('indexprogram.html', prediction_text=prediction_text)

if __name__ == '__main__':
    app.run(debug=True)
