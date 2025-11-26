from fastapi import FastAPI, HTTPException, Header, Depends
from pydantic import BaseModel
import os
from dotenv import load_dotenv
from openai import OpenAI
import google.generativeai as genai

load_dotenv()

app = FastAPI()

API_KEY_SECRET = os.getenv("MY_API_KEY")
OPENAI_API_KEY = os.getenv("OPENAI_API_KEY")
GEMINI_API_KEY = os.getenv("GEMINI_API_KEY")

AI_PROVIDER = os.getenv("AI_PROVIDER", "openai")

openai_client = None
if OPENAI_API_KEY:
    openai_client = OpenAI(api_key=OPENAI_API_KEY)

if GEMINI_API_KEY:
    genai.configure(api_key=GEMINI_API_KEY)

class QuestionRequest(BaseModel):
    question: str

class AnswerResponse(BaseModel):
    answer: str

async def verify_api_key(x_api_key: str = Header(None)):
    if x_api_key != API_KEY_SECRET:
        raise HTTPException(status_code=403, detail="Invalid API Key")
    return x_api_key

def get_ai_response(user_question: str):
    system_prompt = (
        """
        أنت مساعد ذكي متقدم وخبير في تقديم المعلومات لموقع إخباري موثوق. دورك الأساسي هو: الإجابة على استفسارات الزوار بأسلوب مهني، حيادي، دقيق، ومختصر. قواعد الأداء: الأسلوب: يجب أن يكون الرد مهذباً واحترافياً يعكس موثوقية الموقع. المحتوى: التركيز المطلق على الحقائق المثبتة والمعلومات الموضوعية فقط. التجنب: الابتعاد كلياً عن أي آراء شخصية أو تحليلات متحيزة. تجنّب الخوض في الآراء السياسية الحادة أو المثيرة للجدل، والحفاظ على الحياد التام. الإيجاز: تقديم الإجابة بإيجاز مع مراعاة الشمولية عند الضرورة
        """
    )


    try:
        if AI_PROVIDER == "openai" and openai_client:
            response = openai_client.chat.completions.create(
                #model="gpt-5.1",
                model="gpt-5-mini",
                messages=[
                    {"role": "system", "content": system_prompt},
                    {"role": "user", "content": user_question}
                ],
                temperature=0.7
            )
            return response.choices[0].message.content

        elif AI_PROVIDER == "gemini" and GEMINI_API_KEY:
            #model = genai.GenerativeModel('gemini-3-pro-preview')
            model = genai.GenerativeModel('gemini-flash-latest')
            full_prompt = f"{system_prompt}\n\nالسؤال: {user_question}"
            response = model.generate_content(full_prompt)
            return response.text

        else:
            return "عذراً، خدمة الذكاء الاصطناعي غير مفعلة حالياً."

    except Exception as e:
        return f"حدث خطأ أثناء المعالجة: {str(e)}"

@app.post("/ask", response_model=AnswerResponse)
async def ask_question(request: QuestionRequest, api_key: str = Depends(verify_api_key)):
    answer_text = get_ai_response(request.question)
    return {"answer": answer_text}

__main__ = "__main__"
if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="127.0.0.1", port=8001)
