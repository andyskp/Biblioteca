// src/components/BookForm.jsx
import React, { useState, useEffect } from "react";

export default function BookForm({ onBookAdded, selectedBook, onBookUpdated }) {
  const [form, setForm] = useState({
    title: "",
    author: "",
    genre: "",
    available: true,
  });

  // Si llega un libro seleccionado, prellenar el formulario
  useEffect(() => {
    if (selectedBook) {
      setForm({
        title: selectedBook.title || "",
        author: selectedBook.author || "",
        genre: selectedBook.genre || "",
        available: selectedBook.available ?? true,
      });
    }
  }, [selectedBook]);

  const handleChange = (e) => {
    const { name, value, type, checked } = e.target;
    setForm({
      ...form,
      [name]: type === "checkbox" ? checked : value,
    });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");

    const method = selectedBook ? "PUT" : "POST";
    const url = selectedBook
      ? `http://localhost:8000/books/${selectedBook.id}`
      : "http://localhost:8000/books";

    try {
      const res = await fetch(url, {
        method,
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": csrfToken,
        },
        body: JSON.stringify(form),
      });

      if (!res.ok) throw new Error("Error al guardar libro");

      const data = await res.json();

      if (selectedBook) {
        onBookUpdated(data); // actualizar libro en la lista
      } else {
        onBookAdded(data); // agregar libro nuevo
      }

      setForm({ title: "", author: "", genre: "", available: true });
    } catch (err) {
      console.error("❌ Error al guardar:", err.message);
    }
  };

  return (
    <form onSubmit={handleSubmit}>
      <h3>{selectedBook ? "✏️ Editar libro" : "➕ Agregar nuevo libro"}</h3>
      <input name="title" placeholder="Título" value={form.title} onChange={handleChange} required />
      <input name="author" placeholder="Autor" value={form.author} onChange={handleChange} required />
      <input name="genre" placeholder="Género" value={form.genre} onChange={handleChange} required />
      <label>
        <input type="checkbox" name="available" checked={form.available} onChange={handleChange} />
        Disponible
      </label>
      <button type="submit">{selectedBook ? "Actualizar" : "Guardar"}</button>
    </form>
  );
}
