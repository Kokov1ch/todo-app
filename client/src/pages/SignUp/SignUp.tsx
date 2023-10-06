import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import './SignUp.css';

const SignUp = () => {
    const navigate = useNavigate();
    const [redirectToLogin, setRedirectToLogin] = useState(false);

    const handleSignUp = () => {
        // Здесь можно добавить логику для регистрации пользователя
        // После успешной регистрации перенаправляем пользователя на главную страницу
        setRedirectToLogin(true);
    };

    if (redirectToLogin) {
        navigate('/');
    }

    return (
        <div className="signup-container">
            <h1>Регистрация</h1>
            <div className="input-group">
                <label htmlFor="username">Имя пользователя</label>
                <input type="text" id="username" />
            </div>
            <div className="input-group">
                <label htmlFor="email">Email</label>
                <input type="text" id="email" />
            </div>
            <div className="input-group">
                <label htmlFor="password">Пароль</label>
                <input type="password" id="password" />
            </div>
            <div className="input-group">
                <label htmlFor="password">Повторите пароль</label>
                <input type="password" id="repeat-password" />
            </div>
            <button onClick={handleSignUp}>Зарегистрироваться</button>
        </div>
    );
};

export default SignUp;
