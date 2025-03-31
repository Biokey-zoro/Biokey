import mysql from "mysql";

const connection = mysql.createConnection({
    host: "localhost",
    user: "root",
    password: "",
    database: "lockerv1_db"
});

connection.connect(err => {
    if (err) throw err;
    console.log("Database connected!");
});

// ✅ Use `export default` instead of `module.exports`
export default connection;
