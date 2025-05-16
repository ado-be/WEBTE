# Použijeme oficiálny Python image
FROM python:3.11-slim

# Nastavíme pracovný adresár vo vnútri kontajnera
WORKDIR /app

# Skopírujeme všetky súbory z tvojho PC do kontajnera
COPY . .

# Nainštalujeme Python balíky podľa requirements.txt
RUN pip install --no-cache-dir -r requirements.txt

# Spustíme aplikáciu
CMD ["uvicorn", "app.main:app", "--host", "0.0.0.0", "--port", "8000"]
