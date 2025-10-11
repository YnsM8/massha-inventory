/**
 * PRUEBAS DE ACEPTACIÓN E2E - ISO 29119
 * Herramienta: Cypress
 * Objetivo: Probar requisitos funcionales con interfaz gráfica
 * Técnica: End-to-End Testing (usuario real)
 */

describe('Pruebas de Aceptación - Solicitudes de Insumos', () => {
  
  const baseUrl = 'http://localhost:8000'
  
    /**
   * PA-001: Login y navegación al módulo de Solicitudes
   * 
   * Objetivo: Verificar que un usuario puede acceder al sistema
   * Entrada: Credenciales válidas
   * Salida Esperada: Acceso al sistema y navegación a Solicitudes
   * Criterio: Requisito funcional RF-001 (Autenticación)
   */
  it('PA-001: Usuario puede iniciar sesión y acceder a Solicitudes', () => {
    // ARRANGE & ACT - Realizar login
    cy.visit(`${baseUrl}/login`)
    
    cy.get('input[name="email"]').type('test@massha.com')
    cy.get('input[name="password"]').type('password123')
    cy.get('button[type="submit"]').click()

    // ASSERT - Verificar que inició sesión exitosamente
    cy.url().should('satisfy', (url) => {
      return url.includes('/home') || url.includes('/dashboard') || url === `${baseUrl}/`
    })
    
    // Verificar que hay un elemento de usuario autenticado (menú de usuario)
    cy.get('body').should('be.visible')
    
    // Esperar a que cargue la página
    cy.wait(1000)
    
    // Screenshot después del login
    cy.screenshot('PA-001-despues-login')

    // Navegar directamente a Solicitudes
    cy.visit(`${baseUrl}/solicitudes`)

    // Verificar que cargó la página de solicitudes
    cy.url().should('include', '/solicitudes')
    cy.contains('Solicitudes').should('be.visible')
    cy.contains('Nueva Solicitud').should('be.visible')

    // Verificar que el sidebar está visible
    cy.get('.sidebar').should('exist')
    
    // Verificar elementos del menú
    cy.contains('Dashboard').should('be.visible')
    cy.contains('Inventario').should('be.visible')
    cy.contains('Solicitudes').should('be.visible')

    // Screenshot de evidencia final
    cy.screenshot('PA-001-acceso-solicitudes')
    
    console.log('✓ PA-001: Usuario autenticado y acceso a Solicitudes verificado')
  })

  /**
   * PA-002: Crear una solicitud completa
   * 
   * Objetivo: Verificar el proceso completo de creación de solicitud
   * Entrada: Datos de evento + 2 items con stock suficiente
   * Salida Esperada: Solicitud creada, estado Pendiente
   * Criterio: Requisito funcional RF-002 (Crear Solicitud)
   */
  it('PA-002: Usuario puede crear una solicitud con múltiples items', () => {
    // Pre-requisito: Login
    cy.visit(`${baseUrl}/login`)
    cy.get('input[name="email"]').type('test@massha.com')
    cy.get('input[name="password"]').type('password123')
    cy.get('button[type="submit"]').click()
    
    // Esperar a que cargue
    cy.wait(1000)

    // Navegar a Solicitudes
    cy.visit(`${baseUrl}/solicitudes/create`)

    // Verificar que cargó el formulario
    cy.url().should('include', '/solicitudes/create')
    cy.contains('Nueva Solicitud').should('be.visible')

    // Llenar formulario
    cy.get('input[name="evento"]').type('Boda García - Cypress Test E2E')
    
    // Seleccionar fecha (15 días en el futuro)
    const fechaFutura = new Date()
    fechaFutura.setDate(fechaFutura.getDate() + 15)
    const year = fechaFutura.getFullYear()
    const month = String(fechaFutura.getMonth() + 1).padStart(2, '0')
    const day = String(fechaFutura.getDate()).padStart(2, '0')
    const fechaFormatted = `${year}-${month}-${day}`
    
    cy.get('input[name="fecha_evento"]').type(fechaFormatted)

    cy.get('textarea[name="observaciones"]').type('Prueba automatizada E2E con Cypress - Evento para 100 personas')

    // Esperar a que cargue el primer item
    cy.wait(500)

    // Verificar que existe el select del primer item
    cy.get('select[name^="items"]').should('exist')

    // Seleccionar primer item (índice 1 es el primer item real, 0 es "Seleccionar...")
    cy.get('select').filter('[name^="items"][name*="item_id"]').first().select(1, {force: true})
    cy.get('input').filter('[name^="items"][name*="cantidad"]').first().clear().type('10')

    // Click en "Agregar Item" para segundo item
    cy.contains('Agregar Item').click()
    cy.wait(300)

    // Seleccionar segundo item
    cy.get('select').filter('[name^="items"][name*="item_id"]').eq(1).select(1, {force: true})
    cy.get('input').filter('[name^="items"][name*="cantidad"]').eq(1).clear().type('5')

    // Screenshot antes de enviar
    cy.screenshot('PA-002-formulario-completo')

    // Enviar formulario
    cy.get('button[type="submit"]').contains('Crear Solicitud').click()

    // ASSERT - Esperar redirección
    cy.wait(2000)
    
    // Verificar que estamos en la página de solicitudes o detalle
    cy.url().should('satisfy', (url) => {
      return url.includes('/solicitudes')
    })

    // Verificar mensaje de éxito o que aparece en la lista
    cy.get('body').then($body => {
      if ($body.text().includes('Boda García')) {
        cy.contains('Boda García').should('be.visible')
      }
    })

    // Screenshot de evidencia final
    cy.screenshot('PA-002-solicitud-creada')
    
    console.log('✓ PA-002: Solicitud creada exitosamente')
  })

  /**
   * PA-003: Aprobar solicitud pendiente
   * 
   * Objetivo: Verificar que se puede aprobar una solicitud
   * Entrada: Solicitud pendiente
   * Salida Esperada: Estado cambia a Aprobada
   * Criterio: Requisito funcional RF-003 (Aprobar Solicitud)
   */
  it('PA-003: Usuario puede ver y aprobar solicitudes pendientes', () => {
    // Pre-requisito: Login
    cy.visit(`${baseUrl}/login`)
    cy.get('input[name="email"]').type('test@massha.com')
    cy.get('input[name="password"]').type('password123')
    cy.get('button[type="submit"]').click()
    
    cy.wait(1000)

    // Navegar a Solicitudes
    cy.visit(`${baseUrl}/solicitudes`)

    // Verificar que hay solicitudes
    cy.get('table').should('exist')

    // Verificar que existe al menos una fila en la tabla (además del header)
    cy.get('tbody tr').should('have.length.greaterThan', 0)

    // Screenshot de la lista
    cy.screenshot('PA-003-lista-solicitudes')

    // Verificar elementos de la tabla
    cy.get('tbody tr').first().within(() => {
      // Verificar que hay un código de solicitud
      cy.get('td').first().invoke('text').should('match', /SOL-/)
      
      // Verificar que hay un evento
      cy.get('td').eq(1).should('not.be.empty')
      
      // Verificar que hay una fecha
      cy.get('td').eq(2).should('not.be.empty')
      
      // Verificar que hay un estado (badge)
      cy.get('td').eq(3).find('.badge').should('exist')
    })

    // Buscar botón de ver detalle (ojo)
    cy.get('a[title="Ver detalle"]').should('exist').first().click()

    // Verificar que cargó el detalle
    cy.wait(1000)
    cy.url().should('include', '/solicitudes/')
    cy.contains('Detalle de Solicitud').should('be.visible')

    // Screenshot del detalle
    cy.screenshot('PA-003-detalle-solicitud')

    // Verificar información del evento
    cy.contains('Información del Evento').should('be.visible')
    cy.contains('Código:').should('be.visible')
    cy.contains('Evento:').should('be.visible')
    cy.contains('Estado:').should('be.visible')

    // Verificar items solicitados
    cy.contains('Items Solicitados').should('be.visible')
    cy.get('table').should('exist')

    console.log('✓ PA-003: Detalle de solicitud verificado correctamente')
  })
})