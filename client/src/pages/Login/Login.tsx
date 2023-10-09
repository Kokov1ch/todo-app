import React, { useState } from 'react';
import { Link } from "react-router-dom";
import {useNavigate} from 'react-router'
import './Login.scss';
import { useDispatch, useSelector } from 'react-redux';
import { getToken, saveToken } from '../../store/slices/authSlice'

const Login = () => {
    const dispatch = useDispatch();
    const navigate = useNavigate()
    // @ts-ignore
    const authInfo = useSelector((state) => state.auth.authInfo);

    const [username, setUsername] = useState('');
    const [password, setPassword] = useState('');

    const handleLogin = () => {
        // @ts-ignore
        dispatch(getToken({ username, password }));
    };

    if (authInfo.data) {
        dispatch(saveToken());
        navigate('tasks')
    }

    return (
        <div className="login-container">
            <h1>Вход</h1>
            {authInfo.error && <p style={{ color: 'red' }}>{authInfo.error}</p>}
            <div className="input-group">
                <label htmlFor="username">Имя пользователя</label>
                <input type="text" id="username" value={username} onChange={(e) => setUsername(e.target.value)} />
            </div>
            <div className="input-group">
                <label htmlFor="password">Пароль</label>
                <input type="password" id="password" value={password} onChange={(e) => setPassword(e.target.value)} />
            </div>
            <button type="button" onClick={handleLogin} disabled={authInfo.isLoading}>
                {authInfo.isLoading ? 'Вход...' : 'Войти'}
            </button>
            <p>
                Нет учетной записи? <Link to="/signup">Зарегистрируйтесь</Link>
            </p>
        </div>
    );
};

export default Login;
