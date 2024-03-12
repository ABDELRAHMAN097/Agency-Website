import { Route, Routes, BrowserRouter, Outlet } from "react-router-dom";

export default function App() {
  return (
    <div className="App">
      <BrowserRouter>
        <Routes>
          <Route path="/">
            <Route path="about" element={"About"} />
            <Route path="form" element={"Form Page"} />
            <Route path="login" element={"Login Page"} />
            <Route path="*" element={"Page 404"} />
          </Route>
        </Routes>
        <Outlet />
      </BrowserRouter>
    </div>
  );
}
