import React, {useState, useEffect, FC} from 'react';
import { Link } from "react-router-dom";
import {useNavigate} from 'react-router'
import './Login.scss';
import { getToken, saveToken } from "../../store/slices/authSlice"
import { getRefreshToken } from "../../shared/services/JWT/jwtService";
import { refreshingToken } from "../../shared/api";
import {useAppDispatch, useAppSelector} from "../../shared/hooks";

const Login: FC = () => {
    const dispatch = useAppDispatch()
    const navigate = useNavigate()
    // @ts-ignore
    const authInfo = useAppSelector((state) => state.auth.authInfo);

    const [username, setUsername] = useState('');
    const [password, setPassword] = useState('');

    useEffect(() => {
        let refreshToken = getRefreshToken();
        if (refreshToken) {
            // @ts-ignore
            refreshingToken()
                .then(() => {
                    navigate('tasks')
                })
        }
    }, [])

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
            {authInfo.error && <p style={{ color: 'red' }}>{'Неверный логин или пароль'}</p>}
            <p>
                Нет учетной записи? <Link to="/signup">Зарегистрируйтесь</Link>
            </p>
        </div>
    );
};

export default Login;
