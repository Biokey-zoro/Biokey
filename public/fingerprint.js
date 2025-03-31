import express from "express";
import bodyParser from "body-parser";
import cors from "cors";
import webAuthnRoutes from "./webAuthnRoutes.js"; // Add `.js` extension

const app = express();
app.use(cors());
app.use(bodyParser.json());
app.use(express.static("public"));
app.use("/", webAuthnRoutes);

app.listen(3000, () => console.log("Server running on http://localhost:3000"));
