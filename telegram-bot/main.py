from telegram import Update
from telegram.ext import ApplicationBuilder, CommandHandler, ContextTypes
import mysql.connector

# --- Configuración de la base de datos ---
db_config = {
    'host': 'localhost',
    'user': 'alfa_uno',
    'password': 'Cascabel123',
    'database': 'alfa_db'
}

# --- Función para obtener la última temperatura del sensor 1 ---
def get_last_temperature_sensor1():
    conn = mysql.connector.connect(**db_config)
    cursor = conn.cursor()
    cursor.execute("SELECT sensor1_id, sensor1_temp, sensor1_date FROM sensor1 ORDER BY sensor1_date DESC LIMIT 1;")
    row = cursor.fetchone()
    cursor.close()
    conn.close()
    if row:
        return f"Sensor 1: {row[1]}°C\nHora: {row[2]}"
    else:
        return "No hay registros de temperatura."

# --- Función para obtener la última temperatura del sensor 1 ---
def get_last_temperature_sensor2():
    conn = mysql.connector.connect(**db_config)
    cursor = conn.cursor()
    cursor.execute("SELECT sensor2_id, sensor2_temp, sensor2_date FROM sensor2 ORDER BY sensor2_date DESC LIMIT 1;")
    row = cursor.fetchone()
    cursor.close()
    conn.close()
    if row:
        return f"Sensor 2: {row[1]}°C\nHora: {row[2]}"
    else:
        return "No hay registros de temperatura."


# --- Función para obtener un resumen de temperatura del sensor1
def get_daily_summary_sensor1():
    conn = mysql.connector.connect(**db_config)
    cursor = conn.cursor()
    cursor.execute("""
        SELECT DATE(sensor1_date) as fecha
        FROM sensor1
        ORDER BY sensor1_date DESC
        LIMIT 1
    """)
    result = cursor.fetchone()
    if not result:
        return "No hay datos para el sensor 1."

    fecha = result[0]
    cursor.execute("""
        SELECT 
            ROUND(AVG(sensor1_temp), 2),
            MIN(sensor1_temp),
            MAX(sensor1_temp)
        FROM sensor1
        WHERE DATE(sensor1_date) = %s
    """, (fecha,))
    resumen = cursor.fetchone()
    cursor.close()
    conn.close()

    return (
        f"📊 Resumen del {fecha} (Sensor 1):\n"
        f"🌡️ Promedio: {resumen[0]}°C\n"
        f"🔻 Mínima: {resumen[1]}°C\n"
        f"🔺 Máxima: {resumen[2]}°C"
    )

# --- Función para obtener un resumen de temperatura del sensor2
def get_daily_summary_sensor2():
    conn = mysql.connector.connect(**db_config)
    cursor = conn.cursor()
    cursor.execute("""
        SELECT DATE(sensor2_date) as fecha
        FROM sensor2
        ORDER BY sensor2_date DESC
        LIMIT 1
    """)
    result = cursor.fetchone()
    if not result:
        return "No hay datos para el sensor 2."

    fecha = result[0]
    cursor.execute("""
        SELECT 
            ROUND(AVG(sensor2_temp), 2),
            MIN(sensor2_temp),
            MAX(sensor2_temp)
        FROM sensor2
        WHERE DATE(sensor2_date) = %s
    """, (fecha,))
    resumen = cursor.fetchone()
    cursor.close()
    conn.close()

    return (
        f"📊 Resumen del {fecha} (Sensor 2):\n"
        f"🌡️ Promedio: {resumen[0]}°C\n"
        f"🔻 Mínima: {resumen[1]}°C\n"
        f"🔺 Máxima: {resumen[2]}°C"
    )

# --- Función que se ejecuta cuando el usuario escribe /resumen
async def resumen(update: Update, context: ContextTypes.DEFAULT_TYPE):
    resumen1 = get_daily_summary_sensor1()
    resumen2 = get_daily_summary_sensor2()
    await update.message.reply_text(resumen1)
    await update.message.reply_text(resumen2)


# --- Función que se ejecuta cuando el usuario escribe /temperatura ---
async def temperatura(update: Update, context: ContextTypes.DEFAULT_TYPE):
    temperatura_info = get_last_temperature_sensor1()
    await update.message.reply_text(temperatura_info)

    temperatura_info = get_last_temperature_sensor2()
    await update.message.reply_text(temperatura_info)

# --- Funcion de ayuda al escribir /start o /help ---
async def start(update: Update, context: ContextTypes.DEFAULT_TYPE):
    mensaje = (
        "\U0001F44B Hola! Soy tu bot de monitoreo de temperatura.\n\n"
        "Comandos disponibles:\n"
        "/temperatura - Mostrar la ultima temperatura de los sensores\n"
        "/resumen - Mostrar un resumen de la temperatura del día\n"
        "/help - Mostrar este mensaje de ayuda"
    )
    await update.message.reply_text(mensaje)

# --- Inicializa el bot ---
if __name__ == '__main__':
    app = ApplicationBuilder().token('7252834648:AAH0mnwx3LxePmwmagurHvcUI6wDGm8n6xY').build()
    app.add_handler(CommandHandler('temperatura', temperatura))
    app.add_handler(CommandHandler('resumen', resumen))
    app.add_handler(CommandHandler('start', start))
    app.add_handler(CommandHandler('help', start))
    print("Bot corriendo...")
    app.run_polling()
