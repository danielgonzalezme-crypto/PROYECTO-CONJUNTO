# ---- Etapa 1: compilar ----
FROM eclipse-temurin:21-jdk AS build
WORKDIR /app

COPY .mvn .mvn
COPY mvnw pom.xml ./
# Evita el error "bad interpreter" si el archivo quedó con saltos de línea de Windows
RUN sed -i 's/\r$//' mvnw && chmod +x mvnw
RUN ./mvnw -q -B dependency:go-offline

COPY src src
RUN ./mvnw -q -B -DskipTests package

# ---- Etapa 2: ejecutar ----
FROM eclipse-temurin:21-jre
WORKDIR /app
COPY --from=build /app/target/*.jar app.jar

EXPOSE 8080
ENTRYPOINT ["java", "-jar", "app.jar"]
