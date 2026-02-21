<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function home() {return view('public.home');}
    public function profesores() { return view('public.profesores');}
    public function horarios() { return view('public.horarios');}
    public function planes() { return view('public.planes');}
    public function contacto() { return view('public.contacto');}
    public function preinscripcion() { return view('public.preinscripcion');}
    public function elclub() { return view('public.elclub');}
    public function faq() { return view('public.faq');}
}
