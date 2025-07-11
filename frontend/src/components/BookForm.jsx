// src/components/BookForm.jsx
import React, { useState } from "react";

export default function BookForm({ onBookAdded }) {
  const [form, setForm] = useState({
    title: "",
    author: "",
    genre: "",
    available: true,
  });

  const handleChange = (e) => {
    const { name, value, type, checked } = e.target;
    setForm({
      ...form,
      [name]: type === "checkbox" ? checked : value,
    });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      const res = await fetch("http://localhost:8000/books", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(form),
      });

      if (!res.ok) throw new Error("Error al crear libro");

      const nuevoLibro = await res.json();
      onBookAdded(nuevoLibro);
      setForm({ title: "", author: "", genre: "", available: true });
    } catch (err) {
      console.error("❌ Error al guardar:", err.message);
    }
  };

  return (
    <form onSubmit={handleSubmit}>
      <h3>➕ Agregar nuevo libro</h3>
      <input name="title" placeholder="Título" value={form.title} onChange={handleChange} required />
      <input name="author" placeholder="Autor" value={form.author} onChange={handleChange} required />
      <input name="genre" placeholder="Género" value={form.genre} onChange={handleChange} required />
      <label>
        <input type="checkbox" name="available" checked={form.available} onChange={handleChange} />
        Disponible
      </label>
      <button type="submit">Guardar</button>
    </form>
  );
}
