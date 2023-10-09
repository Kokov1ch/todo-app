import React from "react";
import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import { Provider } from "react-redux";
import { Login } from "./pages/Login";
import { SignUp } from "./pages/SignUp";
import {store} from "./store";

const App: React.FC = () => {
    return (
        <div className="app-container">
            <Provider store={store}>
                <Router>
                    <Routes>
                        <Route path="/" element={<Login />} />
                        <Route path="/signup" element={<SignUp />} />
                    </Routes>
                </Router>
            </Provider>
        </div>
    );
};

export default App;