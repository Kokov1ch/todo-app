import React from 'react';
import { Link } from "react-router-dom";
import './Login.css'; // Подключите свой CSS-файл или добавьте стили непосредственно в ваш файл, если вы не используете отдельный файл.

const Login = () => {
    return (
            <div className="login-container">
                <h1>Login</h1>
                <div className="input-group">
                    <label htmlFor="username">Username</label>
                    <input type="text" id="username" />
                </div>
                <div className="input-group">
                    <label htmlFor="password">Password</label>
                    <input type="password" id="password" />
                </div>
                <button>Login</button>
                <p>
                    Нет учетной записи? <Link to="/signup">зарегистрируйтесь</Link>
                </p>
            </div>
    );
};


export default Login;
