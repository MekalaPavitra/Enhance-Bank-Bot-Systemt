<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['userId'])) {
    header('location:login.php');
    exit(); // Ensure the script stops execution after redirecting
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Chatbot</title>
    <style>
        #chatbot-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 300px;
            background: white;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        #chatbot-header {
            background: #007bff;
            color: white;
            padding: 10px;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            cursor: pointer;
        }
        #chatbot-body {
            padding: 10px;
            height: 300px;
            overflow-y: auto;
            display: none;
        }
        #chatbot-input-container {
            padding: 10px;
            display: none;
        }
        #chatbot-input {
            width: 100%;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .user-message {
            background: #007bff;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            max-width: 80%;
            align-self: flex-end;
        }
        .bot-message {
            background: #f1f1f1;
            color: black;
            padding: 5px 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            max-width: 80%;
            align-self: flex-start;
        }
        #chatbot-messages {
            display: flex;
            flex-direction: column;
        }
    </style>
</head>
<body>
    <div id="chatbot-container">
        <div id="chatbot-header">
            <span>Chatbot</span>
            <span id="chatbot-toggle" style="float: right;">-</span>
        </div>
        <div id="chatbot-body">
            <div id="chatbot-messages"></div>
        </div>
        <div id="chatbot-input-container">
            <input type="text" id="chatbot-input" placeholder="Type your message...">
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatbotContainer = document.getElementById('chatbot-container');
            const chatbotHeader = document.getElementById('chatbot-header');
            const chatbotBody = document.getElementById('chatbot-body');
            const chatbotInputContainer = document.getElementById('chatbot-input-container');
            const chatbotInput = document.getElementById('chatbot-input');
            const chatbotToggle = document.getElementById('chatbot-toggle');
            const chatbotMessages = document.getElementById('chatbot-messages');

            let isChatbotOpen = false;

            chatbotHeader.addEventListener('click', function() {
                isChatbotOpen = !isChatbotOpen;
                chatbotBody.style.display = isChatbotOpen ? 'block' : 'none';
                chatbotInputContainer.style.display = isChatbotOpen ? 'block' : 'none';
                chatbotToggle.textContent = isChatbotOpen ? '-' : '+';
            });

            chatbotInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    const userMessage = chatbotInput.value.trim();
                    if (userMessage) {
                        addMessage('user', userMessage);
                        chatbotInput.value = '';
                        getBotResponse(userMessage);
                    }
                }
            });

            function addMessage(sender, message) {
                const messageElement = document.createElement('div');
                messageElement.className = sender === 'user' ? 'user-message' : 'bot-message';
                messageElement.textContent = message;
                chatbotMessages.appendChild(messageElement);
                chatbotBody.scrollTop = chatbotBody.scrollHeight;
            }

            async function getBotResponse(userMessage) {
                try {
                    const response = await fetch('https://api.openai.com/v1/chat/completions', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': 'Bearer sk-ijklmnopqrstuvwxijklmnopqrstuvwxijklmnop', // Replace with your OpenAI API key
                        },
                        body: JSON.stringify({
                            model: 'gpt-3.5-turbo',
                            messages: [
                                { role: 'system', content: 'You are a helpful assistant.' },
                                { role: 'user', content: userMessage }
                            ],
                            max_tokens: 150
                        })
                    });

                    const data = await response.json();
                    if (data.choices && data.choices.length > 0) {
                        const botMessage = data.choices[0].message.content.trim();
                        addMessage('bot', botMessage);
                    } else {
                        throw new Error('No response from the API');
                    }
                } catch (error) {
                    console.error('Error fetching bot response:', error);
                    addMessage('bot', 'Hello, I am Chatbot. How can I help you?');
                }
            }
        });
    </script>
</body>
</html>