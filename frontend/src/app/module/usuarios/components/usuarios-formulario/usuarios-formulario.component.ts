import { Component, EventEmitter, inject, Input, OnChanges, OnInit, Output, SimpleChanges, AfterViewInit, OnDestroy } from '@angular/core';
import {
  FormBuilder,
  FormGroup,
  ReactiveFormsModule,
  Validators,
} from '@angular/forms';
import { UsuariosService } from '../../service/usuario.service';
import { MensajeService } from '../../service/mensaje.service';
import { CommonModule } from '@angular/common';
import { Usuario } from '../../model/usuario.model';

@Component({
  selector: 'app-usuarios-formulario',
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './usuarios-formulario.component.html',
})
export class UsuariosFormularioComponent implements OnChanges, AfterViewInit, OnDestroy {
  @Input() usuario: Usuario | null = null; //se utiliza para recibir el usuario desde la tabla, si es null es porque vamos a crear
  @Output() movimientoUsuario = new EventEmitter<void>(); //se utiliza para actualizar la tabla al momento de terminar de editar o eliminar
  @Output() cerrarModal = new EventEmitter<void>(); //necesitamos este evento para al momento de cerrar y abrir de nuevo el modal, se reinicie

  private fb = inject(FormBuilder);
  private usuarioService = inject(UsuariosService);
  private mensajeService = inject(MensajeService);
  private elementoModal: HTMLElement | null = null; //guardamos la referencia del modal para que no haya errores si dom no ha cargado bien

  formularioUsuario: FormGroup = this.fb.group({
    nombres: [
      '',
      [Validators.required, Validators.minLength(3), Validators.pattern(/^[a-zA-Z\s]*$/)],
    ],
    apellidos: [
      '',
      [Validators.required, Validators.minLength(3), Validators.pattern(/^[a-zA-Z\s]*$/)],
    ],
    telefono: [
      '',
      [Validators.required, Validators.minLength(7), Validators.maxLength(15), Validators.pattern(/^[0-9]*$/),],
    ],
    correo: ['', [Validators.required, Validators.email]],
  });

  //aca manejamos la logica para saber si estamos editando o creando
  ngOnChanges(changes: SimpleChanges): void {
    if (changes['usuario'] && changes['usuario'].currentValue) {
      this.formularioUsuario.patchValue(this.usuario!);
    } else {
      this.formularioUsuario.reset();
    }
  }

  //implementamos este metodo para cuando cargue completamente el dom recuperar el evento de cerrar el modal para evitar errores de datos erroneos en formulario
  ngAfterViewInit(): void {
    this.elementoModal = document.getElementById('crearUsuarioModal');
    if (this.elementoModal) {
      this.elementoModal.addEventListener('hide.bs.modal', this.modalEscondido);
    }
  }

  //con este metodo evitamos el error de pasar de editar a crear, no tenga los datos del editar
  ngOnDestroy(): void {
    if (this.elementoModal) {
      this.elementoModal.removeEventListener('hide.bs.modal', this.modalEscondido);
    }
  }

  guardar() {
    if (this.formularioUsuario.invalid) {
      this.formularioUsuario.markAllAsTouched();
      return;
    }

    const nuevoUsuario: Usuario = this.formularioUsuario.value;

    //manejamos el error que recibimos desde el backend
    const handleServerError = (err: any) => {
      if (err.error?.error && typeof err.error.error === 'string' && err.error.error.toLowerCase().includes('correo')) {
        this.formularioUsuario.get('correo')?.setErrors({ serverError: err.error.error });
      } else {
        this.mensajeService.error(err.error?.error || 'Error inesperado en el servidor');
      }
    };

    if (this.usuario) {
      this.usuarioService.editar(this.usuario.id!, nuevoUsuario).subscribe({
        next: () => {
          this.mensajeService.success('Usuario actualizado con éxito');
          this.movimientoUsuario.emit();
          this.esconderModal();
        },
        error: handleServerError,
      });
    } else {
      this.usuarioService.crear(nuevoUsuario).subscribe({
        next: () => {
          this.mensajeService.success('Usuario creado con éxito');
          this.movimientoUsuario.emit();
          this.esconderModal();
        },
        error: handleServerError,
      });
    }
  }

  //creamos este metodo para que se cierre el modal cuando terminemos de llenar el formulario
  esconderModal() {
    const modalDOM = document.getElementById('crearUsuarioModal');
    if (modalDOM) {
      const modal = (window as any).bootstrap.Modal.getInstance(modalDOM);
      if (modal) {
        modal.hide();
      }
    }
  }

  //creamos la funcion para poder emitir el evento de que se cerro el modal
  private modalEscondido = () => {
    this.cerrarModal.emit();
  };
}
