describe('Pruebas de Roles y Autenticación', () => {
  
  // Configuración antes de cada prueba
  beforeEach(() => {
    // Visitar la página de login
    cy.visit('http://127.0.0.1:8000/login')
  })

  /**
   * PRUEBA 1: Login como Admin y verificar acceso completo
   */
  it('Admin puede hacer login y acceder a todas las secciones', () => {
    // Ingresar credenciales de admin
    cy.get('#email').type('admin@test.com')
    cy.get('#password').type('password123')
    cy.get('.btn-login').click()

    // Verificar redirección al dashboard
    cy.url().should('include', '/dashboard')
    cy.contains('Dashboard').should('be.visible')

    // Verificar que puede acceder a Usuarios (solo admin)
    cy.get('a[href*="/users"]').click()
    cy.url().should('include', '/users')
    cy.contains('Usuarios').should('be.visible')
  })

  /**
   * PRUEBA 2: Login como Producción y verificar restricciones
   */
  it('Producción puede consultar inventario pero no acceder a usuarios', () => {
    // Login como producción
    cy.get('#email').type('produccion@test.com')
    cy.get('#password').type('password123')
    cy.get('.btn-login').click()

    // Debe acceder al dashboard
    cy.url().should('include', '/dashboard')

    // Puede ver inventario
    cy.get('a[href*="/inventory"]').click()
    cy.url().should('include', '/inventory')

    // NO debe ver el enlace de Usuarios en el sidebar
    cy.get('a[href*="/users"]').should('not.exist')
  })

  /**
   * PRUEBA 3: Login como Ventas y verificar restricciones
   */
  it('Ventas puede consultar inventario pero no acceder a usuarios', () => {
    // Login como ventas
    cy.get('#email').type('ventas@test.com')
    cy.get('#password').type('password123')
    cy.get('.btn-login').click()

    // Debe acceder al dashboard
    cy.url().should('include', '/dashboard')

    // Puede ver inventario
    cy.get('a[href*="/inventory"]').click()
    cy.url().should('include', '/inventory')

    // NO debe ver el enlace de Usuarios
    cy.get('a[href*="/users"]').should('not.exist')
  })

  /**
   * PRUEBA 4: Login como Gerencia y verificar solo lectura
   */
  it('Gerencia puede acceder al dashboard y consultar información', () => {
    // Login como gerencia
    cy.get('#email').type('gerencia@test.com')
    cy.get('#password').type('password123')
    cy.get('.btn-login').click()

    // Debe acceder al dashboard
    cy.url().should('include', '/dashboard')
    cy.contains('Dashboard').should('be.visible')

    // Puede ver reportes
    cy.get('a[href*="/reports"]').click()
    cy.url().should('include', '/reports')

    // NO debe ver el enlace de Usuarios
    cy.get('a[href*="/users"]').should('not.exist')
  })

  /**
   * PRUEBA 5: Verificar redirección de usuarios no autenticados
   */
  it('Usuario sin login es redirigido a la página de login', () => {
    // Intentar acceder directamente al dashboard sin login
    cy.visit('http://127.0.0.1:8000/dashboard')

    // Debe redirigir al login
    cy.url().should('include', '/login')
  })

  /**
   * PRUEBA 6: Verificar alertas de error en login
   */
  it('Mostrar alerta cuando el correo no existe', () => {
    // Ingresar correo inexistente
    cy.get('#email').type('noexiste@test.com')
    cy.get('#password').type('cualquiera123')
    cy.get('.btn-login').click()

    // Verificar que aparece SweetAlert2
    cy.get('.swal2-popup').should('be.visible')
    cy.get('.swal2-title').should('contain', 'Correo no encontrado')
  })

  /**
   * PRUEBA BONUS: Verificar que la contraseña incorrecta muestra alerta
   */
  it('Mostrar alerta cuando la contraseña es incorrecta', () => {
    // Usar un correo válido pero contraseña incorrecta
    cy.get('#email').type('admin@test.com')
    cy.get('#password').type('contraseñamala')
    cy.get('.btn-login').click()

    // Verificar que aparece SweetAlert2
    cy.get('.swal2-popup').should('be.visible')
    cy.get('.swal2-title').should('contain', 'Contraseña incorrecta')
  })
})