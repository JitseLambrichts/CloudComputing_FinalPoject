// Bronvermelding Copilot
// Dit is de configuratie voor de SOAP webservice
// Deze zorgt ervoor dat deze door andere services gevonden kan worden

package be.cloud;

import org.springframework.boot.web.servlet.ServletRegistrationBean;
import org.springframework.context.ApplicationContext;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.core.io.ClassPathResource;
import org.springframework.ws.config.annotation.EnableWs;
import org.springframework.ws.config.annotation.WsConfigurerAdapter;
import org.springframework.ws.transport.http.MessageDispatcherServlet;
import org.springframework.ws.wsdl.wsdl11.DefaultWsdl11Definition;
import org.springframework.xml.xsd.SimpleXsdSchema;
import org.springframework.xml.xsd.XsdSchema;


// Configuratie voor de Spring Web Service
// Activeert SOAP en maakt deze beschikbaar voor andere services
@EnableWs
@Configuration
public class WebServiceConfig extends WsConfigurerAdapter {

    // Registreert de SOAP servlet die alle inkomende SOAP-berichten verwerkt.
    @Bean
    public ServletRegistrationBean<MessageDispatcherServlet> messageDispatcherServlet(ApplicationContext applicationContext) {
        MessageDispatcherServlet servlet = new MessageDispatcherServlet();
        servlet.setApplicationContext(applicationContext);
        servlet.setTransformWsdlLocations(true);
        return new ServletRegistrationBean<>(servlet, "/ws/*");
    }

    // Genereert automatisch een WSDL-bestand op basis van het XSD-schema.
    // Dit definieert het contract voor de SOAP service.
    @Bean(name = "football")
    public DefaultWsdl11Definition defaultWsdl11Definition(XsdSchema teamSchema) {
        DefaultWsdl11Definition wsdl11Definition = new DefaultWsdl11Definition();
        wsdl11Definition.setPortTypeName("FootballPort");
        wsdl11Definition.setLocationUri("/ws");
        wsdl11Definition.setTargetNamespace("http://be/cloud/team_statistics");
        wsdl11Definition.setSchema(teamSchema);
        return wsdl11Definition;
    }

    // Laadt het XSD-schema dat de structuur van alle SOAP request/response berichten definieert
    @Bean
    public XsdSchema teamSchema() {
        return new SimpleXsdSchema(new ClassPathResource("team.xsd"));
    }
}