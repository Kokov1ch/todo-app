import React from 'react';
import { Link } from "react-router-dom";
import './Login.scss'; // Подключите свой CSS-файл или добавьте стили непосредственно в ваш файл, если вы не используете отдельный файл.

const Login = () => {
    return (
            <div className="login-container">
                <h1>Вход</h1>
                <div className="input-group">
                    <label htmlFor="username">Имя пользователя</label>
                    <input type="text" id="username" />
                </div>
                <div className="input-group">
                    <label htmlFor="password">Пароль</label>
                    <input type="password" id="password" />
                </div>
                <button>Войти</button>
                <p>
                    Нет учетной записи? <Link to="/signup">Зарегистрируйтесь</Link>
                </p>
            </div>
    );
};


export default Login;
