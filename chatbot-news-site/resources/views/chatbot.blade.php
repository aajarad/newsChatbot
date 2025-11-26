<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chatbot News</title>
    <!-- تضمين CSRF Token للاستخدام في AJAX -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* CSS بسيط لتنظيم الواجهة */
        body { font-family: sans-serif; margin: 50px; }
        #chat-window { border: 1px solid #ccc; padding: 15px; min-height: 200px; max-height: 400px; overflow-y: auto; margin-bottom: 15px; }
        .user-message { text-align: right; color: blue; }
        .bot-answer { text-align: left; color: green; border-bottom: 1px dotted #eee; padding-bottom: 5px; margin-bottom: 5px; }
        #loading-indicator { color: orange; display: none; }
    </style>
</head>
<body>

    <h1>مساعد الأخبار الذكي</h1>

    <!-- 1. مساحة عرض الإجابات -->
    <div id="chat-window">
        <p class="bot-answer"><strong>البوت:</strong> مرحباً بك، كيف يمكنني مساعدتك؟</p>
    </div>

    <!-- 2. حقل الإدخال والزر -->
    <form id="question-form">
        <input type="text" id="question-input" name="question" placeholder="اطرح سؤالك هنا..." style="width: 80%; padding: 10px;">
        <button type="submit" id="send-btn" style="width: 18%; padding: 10px;">إرسال</button>
    </form>

    <p id="loading-indicator">الروبوت يفكر...</p>

    <!-- تضمين مكتبة Axios لإجراء طلبات AJAX بسهولة -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        document.getElementById('question-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const inputField = document.getElementById('question-input');
            const chatWindow = document.getElementById('chat-window');
            const loadingIndicator = document.getElementById('loading-indicator');
            const question = inputField.value.trim();

            if (!question) return;

            // عرض سؤال المستخدم
            chatWindow.innerHTML += `<p class="user-message"><strong>أنت:</strong> ${question}</p>`;
            chatWindow.scrollTop = chatWindow.scrollHeight; // التمرير للأسفل

            // تفعيل مؤشر التحميل وتعطيل الزر
            loadingIndicator.style.display = 'block';
            document.getElementById('send-btn').disabled = true;

            // إعداد البيانات و CSRF Token
            const data = {
                question: question,
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            };

            // إرسال طلب AJAX إلى Laravel Controller
            axios.post("{{ route('chatbot.ask') }}", data)
                .then(function (response) {
                    // في حال النجاح
                    const answer = response.data.answer;
                    chatWindow.innerHTML += `<p class="bot-answer"><strong>البوت:</strong> ${answer}</p>`;
                })
                .catch(function (error) {
                    // في حال وجود خطأ
                    let errorMessage = 'حدث خطأ غير متوقع.';
                    if (error.response && error.response.data && error.response.data.message) {
                        errorMessage = error.response.data.message;
                    }
                    chatWindow.innerHTML += `<p class="bot-answer" style="color: red;"><strong>خطأ:</strong> ${errorMessage}</p>`;
                })
                .finally(function () {
                    // عند الانتهاء (سواء نجاح أو فشل)
                    loadingIndicator.style.display = 'none';
                    document.getElementById('send-btn').disabled = false;
                    inputField.value = ''; // مسح حقل الإدخال
                    chatWindow.scrollTop = chatWindow.scrollHeight;
                });
        });
    </script>

</body>
</html>
